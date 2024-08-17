import { Text, Button } from "@mantine/core";
import { styles } from "@/src/data";
import React from "react";
import { TouchableOpacity } from "react-native";

const Contact = () => {
  const handleFeedbackClick = () => {
    window.location.href = "mailto:support@uniquest.com.ng";
  };

  return (
    <div className={`${styles.body} text-text bg-primary pt-28 pb-10 h-screen`}>
      <Text className="text-2xl font-bold mb-4">Contact Us</Text>
      <Text className="text-lg mb-2">
        For any inquiries or support, please reach out to us through the
        following channels:
      </Text>
      <Text className="text-lg">
        <strong>Email:</strong> support@uniquest.com.ng
      </Text>
      <Text className="text-lg">
        <strong>Phone:</strong> +234 808 420 4157
      </Text>
      <Text className="text-lg">
        <strong>Address:</strong> No 15, Ugbomro Road, Effurun
      </Text>
      <Text className="text-lg my-4">
        We are here to assist you with any questions or issues you may have.
        Don't hesitate to contact us for any further information or support.
      </Text>
      <TouchableOpacity>
        <Button onClick={handleFeedbackClick} className="bg-secondary text-primary" size="md">
          Give Feedback
        </Button> 
      </TouchableOpacity>
      <Text className="text-center mt-10 text-sm">
        &copy; {new Date().getFullYear()} UniQuest. All rights reserved.
      </Text>
    </div>
  );
};

export default Contact;
