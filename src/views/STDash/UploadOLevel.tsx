import { api } from "@/src/api";
import { grades, styles, subjects } from "@/src/data";
import {
  Button,
  Card,
  Container,
  LoadingOverlay,
  NumberInput,
  Select,
  Text,
  TextInput,
} from "@mantine/core";
import axios from "axios";
import { useEffect, useRef, useState } from "react";
import { TouchableOpacity } from "react-native";
import CryptoJS from "crypto-js";
import { notifications } from "@mantine/notifications";
import { useSpring, animated } from "@react-spring/web";
import SuccessModal from "@/src/components/SuccessModal";
import { router } from "expo-router";

const UploadOLevel = () => {
  const [visible, setVisible] = useState(false);
  const [uniqueId, setUniqueId] = useState("");
  const [selectedSubject, setSelectedSubject] = useState(null);
  const [selectedGrade, setSelectedGrade] = useState(null);
  const [subjectGradeArray, setSubjectGradeArray] = useState([]);
  const [jambScore, setJambScore] = useState(0);
  const [modalText, setModalText] = useState("");
  const successModalRef = useRef(null);

  const addSubjectGrade = () => {
    if (subjectGradeArray.length >= 5) {
      notifications.show({
        title: "Limit Reached",
        message: "You cannot add more than 5 subjects.",
        color: "red",
      });
      return;
    }

    if (selectedSubject && selectedGrade) {
      const newEntry = `${selectedSubject} => ${selectedGrade}`;
      ///@ts-ignore
      setSubjectGradeArray([...subjectGradeArray, newEntry]);
      setSelectedSubject(null);
      setSelectedGrade(null);
    }
  };

  const secretKey =
    "21d1f43eee6a5780499e81575231952e7dd1f88274f72f6d0f78ffe213944aa9";

  const toggleVisibility = () => {
    setVisible((prevVisible) => !prevVisible);
  };

  const handleOpenSuccessModal = (text: string) => {
    setModalText(text);
    setTimeout(() => {
      if (successModalRef.current) {
        //@ts-ignore
        successModalRef.current.openModal();
      }
    }, 0);
  };

  useEffect(() => {
    // Retrieve the unique ID from localStorage
    const encryptedUniqueId = localStorage.getItem("ala");
    const decryptedUniqueId = CryptoJS.AES.decrypt(
      encryptedUniqueId,
      secretKey
    );

    // Prepare the data to be sent to the backend
    const data = {
      student_unique_id: decryptedUniqueId.toString(CryptoJS.enc.Utf8),
    };

    setUniqueId(data.student_unique_id);
  }, []);

  // Slide-in animation
  const slideInStyles = useSpring({
    from: { transform: "translateY(100%)" },
    to: { transform: "translateY(0%)" },
    config: { tension: 220, friction: 30 },
  });

  const handleSubmit = async () => {
    if (subjectGradeArray.length > 0 && jambScore) {
      toggleVisibility();
      const stringifiedSubjectGrade = JSON.stringify(subjectGradeArray);

      axios
        .post(
          api.uploadOLevel,
          {
            unique_id: uniqueId,
            subject_grade: stringifiedSubjectGrade,
            jamb_score: jambScore,
          },
          {
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
          }
        )
        .then((response) => {
          toggleVisibility();
          handleOpenSuccessModal(response.data.message);
          if (response.data.status) {
            setTimeout(() => {
              router.replace("/student/upload");
            }, 1500);
          }
        })
        .catch(() => {
          toggleVisibility();
          handleOpenSuccessModal("Failed to upload olevel & Jamb.");
        });
    }
  };

  return (
    <Container
      className={`py-32 ${styles.body} text-text bg-primary h-[300vh]`}
    >
      <LoadingOverlay
        visible={visible}
        zIndex={1000}
        overlayProps={{ blur: 2 }}
        loaderProps={{ color: "#FFD700", type: "bars" }}
      />
      <animated.div style={slideInStyles}>
        <h2 className="text-2xl font-bold mb-4">Upload O'Level Results</h2>

        <div className="mb-4">
          <Select
            label="Select Subject"
            placeholder="Pick a subject"
            data={subjects}
            value={selectedSubject}
            onChange={setSelectedSubject}
            rightSectionWidth={0}
            searchable
            clearable
          />
        </div>

        <div className="mb-4">
          <Select
            label="Select Grade"
            placeholder="Pick a grade"
            data={grades}
            value={selectedGrade}
            onChange={setSelectedGrade}
            rightSectionWidth={0}
            clearable
          />
        </div>

        <Button
          onClick={addSubjectGrade}
          className="mb-4 bg-blue-500 hover:bg-blue-600 text-white"
        >
          Add Subject & Grade
        </Button>

        <ul className="mb-4">
          {subjectGradeArray.map((item, index) => (
            <li key={index} className="mb-1 text-sm text-secondary">
              {item}
            </li>
          ))}
        </ul>

        <div className="mb-4">
          <NumberInput
            label="Enter JAMB Score"
            placeholder="e.g. 250"
            value={jambScore}
            onChange={setJambScore}
            rightSectionWidth={0}
          />
        </div>

        <Button
          onClick={handleSubmit}
          className="bg-green-500 hover:bg-green-600 text-white"
        >
          Upload Results
        </Button>
      </animated.div>
      <SuccessModal ref={successModalRef} text={modalText} />
    </Container>
  );
};

export default UploadOLevel;
