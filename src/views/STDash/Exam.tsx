import { useState, useEffect, useRef } from "react";
import {
  Button,
  Card,
  Container,
  LoadingOverlay,
  Radio,
  Select,
  Text,
} from "@mantine/core";
import { api } from "@/src/api";
import axios from "axios";
import { useSpring, animated } from "@react-spring/web";
import SuccessModal from "@/src/components/SuccessModal";
import { notifications } from "@mantine/notifications";
import CryptoJS from "crypto-js";
import { styles } from "@/src/data";
import { TouchableOpacity } from "react-native";
import { router } from "expo-router";

interface Question {
  exam_id: string;
  question: string;
  options: string[];
}

const PostUTMEExam = () => {
  const [visible, setVisible] = useState(false);
  const [uniqueId, setUniqueId] = useState<string>("");
  const [questions, setQuestions] = useState<Question[]>([]);
  const [answers, setAnswers] = useState<{ [key: string]: string }>({});
  const [modalText, setModalText] = useState<string>("");
  const successModalRef = useRef<{ openModal: () => void } | null>(null);
  const [timer, setTimer] = useState<number>(3600); // 1 hour in seconds
  const [examStarted, setExamStarted] = useState(false);

  const slideInStyles = useSpring({
    from: { transform: "translateY(100%)" },
    to: { transform: "translateY(0%)" },
    config: { tension: 220, friction: 30 },
  });

  const secretKey =
    "21d1f43eee6a5780499e81575231952e7dd1f88274f72f6d0f78ffe213944aa9";

  useEffect(() => {
    const encryptedUniqueId = localStorage.getItem("ala");
    if (encryptedUniqueId) {
      const decryptedUniqueId = CryptoJS.AES.decrypt(
        encryptedUniqueId,
        secretKey
      );
      setUniqueId(decryptedUniqueId.toString(CryptoJS.enc.Utf8));
    }
  }, []);

  const startTimer = () => {
    const countdown = setInterval(() => {
      setTimer((prevTime) => {
        if (prevTime <= 0) {
          clearInterval(countdown);
          handleSubmit(); // Auto-submit when time is up
          return 0;
        }
        return prevTime - 1;
      });
    }, 1000);
  };

  const toggleVisibility = (): void => {
    setVisible((prevVisible) => !prevVisible);
  };

  const handleOpenSuccessModal = (text: string): void => {
    setModalText(text);
    setTimeout(() => {
      if (successModalRef.current) {
        successModalRef.current.openModal();
      }
    }, 0);
  };

  const fetchExams = async () => {
    toggleVisibility();
    try {
      const response = await axios.post(
        api.fetchExam,
        { unique_id: uniqueId },
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        }
      );
      toggleVisibility();
      if (response.data.questions) {
        const parsedQuestions = response.data.questions.map((q: any) => {
          // Convert set-like string to JSON array string
          const jsonString = q.options
            .replace(/^\{|\}$/g, "") // Remove curly braces
            .split(",") // Split by comma
            .map((item: string) => item.trim().replace(/^"|"$/g, "")) // Trim and remove extra quotes
            .map((item: string) => `"${item}"`); // Add quotes for JSON
          return {
            ...q,
            options: JSON.parse(`[${jsonString.join(",")}]`),
          };
        });

        const randomizedQuestions = shuffleArray(parsedQuestions);
        setQuestions(randomizedQuestions);
        startTimer(); // Start the timer after questions are fetched
        setExamStarted(true);
      } else if (response.data.message) {
        handleOpenSuccessModal(response.data.message);
      }
    } catch (error) {
      toggleVisibility();
      handleOpenSuccessModal("Failed to fetch exam questions.");
    }
  };

  const handleAnswerChange = (
    questionId: string,
    selectedOption: string
  ): void => {
    setAnswers({ ...answers, [questionId]: selectedOption });
  };

  const handleSubmit = async (): Promise<void> => {
    if (Object.keys(answers).length === questions.length) {
      toggleVisibility();
      try {
        const response = await axios.post(
          api.submitExam,
          {
            unique_id: uniqueId,
            answers,
          },
          {
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
          }
        );
        toggleVisibility();
        handleOpenSuccessModal(response.data.message);
        resetExam();
        if (response.data.status) {
          setTimeout(() => {
            router.replace("/student/exam");
          }, 1500);
        }
      } catch (error) {
        toggleVisibility();
        handleOpenSuccessModal("Failed to submit the exam.");
      }
    } else {
      notifications.show({
        title: "Incomplete Exam",
        message: "Please answer all questions before submitting.",
        color: "red",
      });
    }
  };

  const shuffleArray = <T,>(array: T[]): T[] => {
    return array.sort(() => Math.random() - 0.5);
  };

  const resetExam = (): void => {
    setQuestions([]);
    setAnswers({});
    setExamStarted(false);
  };

  const formatTime = (time: number): string => {
    const minutes = Math.floor(time / 60);
    const seconds = time % 60;
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
  };

  console.log(answers);

  return (
    <Container className={`py-32 bg-primary text-text h-full ${styles.body}`}>
      <LoadingOverlay
        visible={visible}
        overlayProps={{ blur: 2 }}
        loaderProps={{ color: "#FFD700", type: "bars" }}
      />
      <animated.div style={slideInStyles}>
        <h2 className="text-2xl font-bold mb-8 text-center">Post-UTME Exam</h2>
        {examStarted && (
          <Text className="mb-4 absolute top-8 right-4 font-bold text-xl">
            Time Remaining: {formatTime(timer)}
          </Text>
        )}
        {questions.length === 0 ? (
          <div>
            <TouchableOpacity>
              <Button
                onClick={fetchExams}
                className="mb-4 bg-secondary text-primary"
              >
                Start Exam
              </Button>
            </TouchableOpacity>
            <Text className="mb-4">
              Please read the following instructions carefully before starting
              the exam.
              <br />
              <br />
              - Answer all questions.
              <br />
              <br />
              - Ensure that you do not refresh the page during the exam.
              <br />
              <br />
              - Time is one hour, failure to submit ontime means exam will be
              submitted automatically.
              <br />
              <br />- Once Submitted check your email for admission status
            </Text>
          </div>
        ) : (
          <div>
            {questions.map((question, index) => (
              <Card key={question.exam_id} className="mb-8 bg-secondary">
                <Text>{`${index + 1}. ${question.question}`}</Text>
                <Radio.Group
                  value={answers[question.exam_id]}
                  onChange={(value) =>
                    handleAnswerChange(question.exam_id, value || "")
                  }
                  name={`question-${question.exam_id}`}
                  className="mt-2"
                >
                  {question.options.map((option) => (
                    <Radio
                      key={option}
                      value={option}
                      label={option}
                      className="mb-2"
                      color="#121212"
                    />
                  ))}
                </Radio.Group>
              </Card>
            ))}

            <TouchableOpacity>
              <Button
                onClick={handleSubmit}
                className={`mt-4 ${
                  Object.keys(answers).length === questions.length
                    ? "bg-secondary "
                    : "bg-gray-500"
                } text-primary`}
                disabled={Object.keys(answers).length !== questions.length}
              >
                Submit Exam
              </Button>
            </TouchableOpacity>
          </div>
        )}
      </animated.div>
      <SuccessModal ref={successModalRef} text={modalText} />
    </Container>
  );
};

export default PostUTMEExam;
