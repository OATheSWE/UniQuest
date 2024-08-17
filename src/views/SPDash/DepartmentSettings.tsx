import {
    Button,
    Container,
    LoadingOverlay,
    NumberInput,
    Select,
    TextInput,
    Textarea,
  } from "@mantine/core";
  import axios from "axios";
  import { useEffect, useRef, useState } from "react";
  import CryptoJS from "crypto-js";
  import { notifications } from "@mantine/notifications";
  import { useSpring, animated } from "@react-spring/web";
  import SuccessModal from "@/src/components/SuccessModal";
  import { api } from "@/src/api"; // Import your API endpoints here
import { TouchableOpacity } from "react-native";
import { router } from "expo-router";
  
  const DepartmentSettings = () => {
    const [visible, setVisible] = useState(false);
    const [visible2, setVisible2] = useState(true);
    const [uniqueId, setUniqueId] = useState("");
    const [cutOffMark, setCutOffMark] = useState(0);
    const [difficultyLevel, setDifficultyLevel] = useState("medium");
    const [additionalRequirements, setAdditionalRequirements] = useState("");
    const [modalText, setModalText] = useState("");
    const successModalRef = useRef(null);
  
    const secretKey =
      "21d1f43eee6a5780499e81575231952e7dd1f88274f72f6d0f78ffe213944aa9";
  
    const toggleVisibility = () => {
      setVisible((prevVisible) => !prevVisible);
    };

    const toggleVisibility2 = () => {
        setVisible2((prevVisible) => !prevVisible);
      };
  
    const handleOpenSuccessModal = (text) => {
      setModalText(text);
      setTimeout(() => {
        if (successModalRef.current) {
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
      ).toString(CryptoJS.enc.Utf8);
  
      setUniqueId(decryptedUniqueId);
  
      // Fetch existing settings using the unique ID
      axios
        .post(
          api.fetchDPSET, // Your PHP endpoint for fetching department settings
          {
            unique_id: decryptedUniqueId,
          },
          {
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
          }
        )
        .then((response) => {
          if (response.data.status) {
            const data = response.data.settings;
            setCutOffMark(data.cut_off_mark);
            setDifficultyLevel(data.difficulty_level);
            setAdditionalRequirements(data.additional_requirements);
            toggleVisibility2();
          } else {
            notifications.show({
              title: "Error",
              message: response.data.message,
              color: "red",
            });
            toggleVisibility2();
          }
        })
        .catch(() => {
          notifications.show({
            title: "Error",
            message: "Failed to fetch department settings.",
            color: "red",
          });
          toggleVisibility2();

        });
    }, []);
  
    const handleSubmit = async () => {
      toggleVisibility();
  
      axios
        .post(
          api.setDPSET, // Your PHP endpoint for updating department settings
          {
            unique_id: uniqueId,
            cut_off_mark: cutOffMark,
            difficulty_level: difficultyLevel,
            additional_requirements: additionalRequirements,
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
              router.replace("/supervisor/settings");
            }, 1500);
          }
        })
        .catch(() => {
          toggleVisibility();
          handleOpenSuccessModal("Failed to update department settings.");
        });
    };
  
    // Slide-in animation
    const slideInStyles = useSpring({
      from: { transform: "translateY(100%)" },
      to: { transform: "translateY(0%)" },
      config: { tension: 220, friction: 30 },
    });
  
    return (
      <Container
        className={`py-32 bg-primary h-full text-text`}
      >
        <LoadingOverlay
          visible={visible}
          zIndex={1000}
          overlayProps={{ blur: 2 }}
          loaderProps={{ color: "#FFD700", type: "bars" }}
        />
        <LoadingOverlay
          visible={visible2}
          zIndex={1000}
          overlayProps={{ blur: 2 }}
          loaderProps={{ color: "#FFD700", type: "bars" }}
        />
        <animated.div style={slideInStyles}>
          <h2 className="text-2xl font-bold mb-4 text-center">Department Settings</h2>
  
          <div className="mb-4">
            <NumberInput
              label="Cut Off Mark"
              placeholder="Enter cut off mark"
              value={cutOffMark}
              onChange={setCutOffMark}
              rightSectionWidth={0}
            />
          </div>
  
          <div className="mb-4">
            <Select
              label="Difficulty Level"
              placeholder="Select difficulty level"
              data={[
                { value: "medium", label: "Medium" },
                { value: "hard", label: "Hard" },
                { value: "extreme", label: "Extreme" },
              ]}
              value={difficultyLevel}
              onChange={setDifficultyLevel}
              rightSectionWidth={0}
            />
          </div>
  
          <div className="mb-4">
            <Textarea
              label="Additional Requirements"
              placeholder="Enter additional requirements"
              value={additionalRequirements}
              onChange={(event) => setAdditionalRequirements(event.target.value)}
              rightSectionWidth={0}
              minRows={4}
              autosize
            />
          </div>
  
        <TouchableOpacity> 
          <Button
            onClick={handleSubmit}
            className="bg-secondary text-primary"
            size="md"
            mt={`xl`}
          >
            Update Settings
          </Button>
          </TouchableOpacity>
        </animated.div>
        <SuccessModal ref={successModalRef} text={modalText} />
      </Container>
    );
  };
  
  export default DepartmentSettings;
  