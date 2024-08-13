import React, { useState } from "react";
import axios from "axios";
import { useForm } from "@mantine/form";
import {
  TextInput,
  Select,
  Button,
  Notification,
  Text,
  LoadingOverlay,
} from "@mantine/core";
import { styles } from "@/src/data";
import { DateInput } from "@mantine/dates";
import SuccessModal from "../SuccessModal";
import { useSpring, animated } from "@react-spring/web";
import { TouchableOpacity } from "react-native";
import { router } from "expo-router";
import { api } from "@/src/api";

const departments = [
  "Computer Science",
  "Electrical Engineering",
  "Business Administration",
  "Law",
];

const Signup = () => {
  const [visible, setVisible] = useState(false);
  const [modalText, setModalText] = React.useState("");
  const [userType, setUserType] = useState(""); // State to manage user type
  const successModalRef = React.useRef(null);

  const form = useForm({
    initialValues: {
      name: "",
      department: "",
      email: "",
      userType: "",
      dob: "",
      homeAddress: "",
      phoneNumber: "",
    },

    validate: {
      name: (value) => (value ? null : "Name is required"),
      department: (value) =>
        (userType === "student" || userType === "lecturer") && value
          ? null
          : "Department is required",
      email: (value) =>
        /^\S+@\S+$/.test(value) ? null : "Invalid email address",
      userType: (value) => (value ? null : "User type is required"),
      dob: (value) =>
        userType === "student" && !value ? "Date of Birth is required" : null,
      homeAddress: (value) =>
        userType === "student" && !value ? "Home address is required" : null,
      phoneNumber: (value) =>
        userType === "student" && !/^\d{11}$/.test(value)
          ? "Enter a valid Nigerian phone number"
          : null,
    },
  });

  const handleOpenSuccessModal = (text: any) => {
    setModalText(text);
    setTimeout(() => {
      if (successModalRef.current) {
        ///@ts-ignore
        successModalRef.current.openModal();
      }
    }, 0);
  };

  const toggleVisibility = () => {
    setVisible((prevVisible) => !prevVisible);
  };

  const handleSubmit = async (values: typeof form.values) => {
    toggleVisibility();
    try {
      const response = await axios.post(api.signup, values, {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      });
      if (response.data.status) {
        if (values.userType === "student") {
          toggleVisibility();
          handleOpenSuccessModal(response.data.message);
          form.reset();
          setTimeout(() => {
            router.push(response.data.payment_url);
          }, 1500);
        } else if (values.userType === "lecturer") {
          toggleVisibility();
          handleOpenSuccessModal(response.data.message);
          form.reset();
          setTimeout(() => {
            router.push("/auth/login");
          }, 1500);
        }
      } else {
        toggleVisibility();
        handleOpenSuccessModal(response.data.message);
        form.reset();
      }
    } catch (error: any) {
      toggleVisibility();
      handleOpenSuccessModal(
        error.response.data.message || "An error occurred"
      );
    }
  };

  const slideInStyles = useSpring({
    from: { transform: "translateY(100%)" },
    to: { transform: "translateY(0%)" },
    config: { tension: 220, friction: 30 },
  });

  return (
    <div
      className={`w-full bg-primary text-text flex justify-center items-center ${
        form.values.userType === "student" ? "h-auto py-28" : "h-screen"
      } flex-col ${styles.body}`}
    >
      <LoadingOverlay
        visible={visible}
        zIndex={1000}
        overlayProps={{ blur: 2 }}
        loaderProps={{ color: "#FFD700", type: "bars" }}
      />
      <animated.div style={slideInStyles} className={`max-w-[600px] w-full`}>
        <h1 className="text-2xl font-bold mb-4 text-center">
          Create an account
        </h1>
        <Text className="text-[13px] py-2 text-center">
          Create an account to access our services, it takes less than a minute.
        </Text>
        <form
          onSubmit={form.onSubmit(handleSubmit)}
          className="max-w-[600px] w-full"
        >
          <Select
            label="User Type"
            placeholder="Select user type"
            data={["student", "lecturer"]}
            {...form.getInputProps("userType")}
            error={form.errors.userType}
            mb={`lg`}
            ///@ts-nocheck
            onChange={(value) => {
              form.setFieldValue("userType", value);
              setUserType(value);
            }}
            rightSectionWidth={0}
            comboboxProps={{
              transitionProps: { transition: "pop", duration: 200 },
            }}
          />

          {userType && (
            <>
              <TextInput
                label="Full Name"
                placeholder="Enter your full name"
                {...form.getInputProps("name")}
                error={form.errors.name}
                mb={`lg`}
              />
              <TextInput
                label="Email"
                placeholder="Enter your email"
                {...form.getInputProps("email")}
                error={form.errors.email}
                mb={`lg`}
              />
              <Select
                label="Department"
                placeholder="Select your department"
                data={departments}
                {...form.getInputProps("department")}
                error={form.errors.department}
                mb={`lg`}
                rightSectionWidth={0}
                comboboxProps={{
                  transitionProps: { transition: "pop", duration: 200 },
                }}
              />
            </>
          )}

          {userType === "student" && (
            <>
              <DateInput
                label="Date of Birth"
                placeholder="Select your date of birth"
                {...form.getInputProps("dob")}
                error={form.errors.dob}
                mb={`lg`}
              />
              <TextInput
                label="Home Address"
                placeholder="Enter your home address"
                {...form.getInputProps("homeAddress")}
                error={form.errors.homeAddress}
                mb={`lg`}
              />
              <TextInput
                label="Phone Number"
                placeholder="Enter your phone number"
                {...form.getInputProps("phoneNumber")}
                error={form.errors.phoneNumber}
                mb={`lg`}
              />
            </>
          )}

          <div className="flex justify-center items-center">
            <Button
              type="submit"
              className="mt-4 bg-secondary border-[1.5px] text-primary border-secondary px-14 h-[40px] rounded-md transition duration-300 hover:bg-transparent"
            >
              Signup
            </Button>
          </div>
        </form>
        <div className="text-[13px] font-normal text-center text-text mt-6 flex justify-center items-center max-[380px]:flex-col">
          Already have an account on UniQuest?
          <TouchableOpacity
            onPress={() => {
              router.replace("/auth/login");
            }}
          >
            <Text className="text-secondary font-bold pl-1 -mt-1 max-[380px]:mt-3">
              Log In
            </Text>
          </TouchableOpacity>
        </div>
      </animated.div>
      <SuccessModal ref={successModalRef} text={modalText} />
    </div>
  );
};

export default Signup;
