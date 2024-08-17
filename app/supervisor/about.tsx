import { Text } from "@mantine/core";
import { styles } from "@/src/data";
import React from "react";

const About = () => {
  return (
    <div className={`${styles.body} text-text bg-primary pt-28 pb-10`}>
      <Text className="text-2xl font-bold mb-4">
        About UniQuest 
      </Text>
      <Text className="text-lg">
        The UniQuest is designed to streamline the
        process of university entrance examinations in Nigeria. This platform
        simplifies the registration, examination, and result processing for
        both students and lecturers. With UniQuest, students can easily register
        for the Post-UTME, submit their O-level results, and take their exams
        in a secure, timed environment.
      </Text>
      <Text className="text-lg mt-4">
        For lecturers, UniQuest offers robust tools for managing exam content,
        setting difficulty levels, and evaluating student performance. The
        system automatically calculates and aggregates scores from JAMB, WAEC,
        and Post-UTME exams to provide a comprehensive evaluation for each
        candidate. Additionally, it supports efficient communication between
        students and lecturers, ensuring a smooth and transparent admission
        process.
      </Text>
      <Text className="text-lg mt-4">
        UniQuest is built with an emphasis on accuracy, efficiency, and user
        experience. Our goal is to provide a reliable and user-friendly platform
        that enhances the entrance examination process and supports educational
        institutions in their admissions efforts.
      </Text>
      <Text className="text-center mt-10 text-sm">
        &copy; {new Date().getFullYear()} UniQuest. All rights reserved.
      </Text>
    </div>
  );
};

export default About;
