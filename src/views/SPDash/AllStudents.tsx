import { api } from "@/src/api";
import { styles } from "@/src/data";
import { Card, Container, LoadingOverlay, Text } from "@mantine/core";
import axios from "axios";
import { useEffect, useState } from "react";
import { TouchableOpacity } from "react-native";
import CryptoJS from "crypto-js";
import { notifications } from "@mantine/notifications";
import { useSpring, animated } from "@react-spring/web";

interface Student {
  student_name: string;
  student_id: string;
  score: number;
  aggregate: number;  
}

const AllStudents = () => {
  const [visible, setVisible] = useState(true);
  const [students, setStudents] = useState<Student[]>([]);
  const [filteredStudents, setFilteredStudents] = useState<Student[]>([]);
  const secretKey =
    "21d1f43eee6a5780499e81575231952e7dd1f88274f72f6d0f78ffe213944aa9";

  const toggleVisibility = () => {
    setVisible((prevVisible) => !prevVisible);
  };

  useEffect(() => {
    // Retrieve the unique ID from localStorage
    const encryptedUniqueId = localStorage.getItem("ala");
    const decryptedUniqueId = CryptoJS.AES.decrypt(
      encryptedUniqueId || '',
      secretKey
    ).toString(CryptoJS.enc.Utf8);

    // Prepare the data to be sent to the backend
    const data = {
      uniqueId: decryptedUniqueId,
    };

    // Fetch students based on the cut off mark
    axios
      .post(api.allStudents, data, {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      })
      .then((response) => {
        const responseData = response.data;
        if (responseData.status) {
          setStudents(responseData.students);
          setFilteredStudents(responseData.students);
        } else {
          notifications.show({
            message: responseData.message || 'Failed to fetch students',
            color: 'red',
          });
        }
        toggleVisibility();
      })
      .catch((err) => {
        toggleVisibility();
        notifications.show({
          message: "A network error occurred",
          color: 'red',
        });
      });
  }, []);

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    const query = e.target.value.toLowerCase();
    if (query) {
      setFilteredStudents(
        students.filter((student) =>
          student.student_name.toLowerCase().includes(query)
        )
      );
    } else {
      setFilteredStudents(students);
    }
  };

  // Slide-in animation
  const slideInStyles = useSpring({
    from: { transform: "translateY(100%)" },
    to: { transform: "translateY(0%)" },
    config: { tension: 220, friction: 30 },
  });

  return (
    <Container className={`py-32 ${styles.body} text-text bg-primary h-[300vh]`}>
      <LoadingOverlay
        visible={visible}
        zIndex={1000}
        overlayProps={{ blur: 2 }}
        loaderProps={{ color: "#D8BFD8", type: "bars" }}
      />
      <animated.div style={slideInStyles}>
        <Text className="text-2xl font-bold mb-4 text-center">
          Students List
        </Text>
        <input
          type="text"
          placeholder="Search students..."
          onChange={handleSearch}
          className="outline-0 w-full px-3 py-2 my-8 rounded-lg bg-transparent border-secondary border-[1.5px] text-text placeholder:text-text"
        />
        <div>
          {filteredStudents.map((student) => (
            <TouchableOpacity key={student.student_id}> 
              <Card
                shadow="xl"
                padding="lg"
                className="mb-4 cursor-pointer bg-secondary text-primary rounded-lg"
              >
                <Text className="font-semibold text-[15px] mb-4">
                  {student.student_name}<br />{student.student_id}
                </Text>
                <Text className="mb-4">Post UTME: {student.score} </Text>
                <Text className="">Aggregate: {student.aggregate}</Text>
              </Card>
            </TouchableOpacity>
          ))}
        </div>
      </animated.div>
    </Container>
  );
};

export default AllStudents;
