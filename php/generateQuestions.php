<?php
include 'db.php';

$questions = [
    // Mathematics - Extreme
    [
        'question' => 'What is the square root of 144?',
        'options' => ['10', '11', '12', '13'],
        'answer' => '12',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'If a = 2 and b = 3, what is the value of a² + b²?',
        'options' => ['9', '13', '10', '14'],
        'answer' => '13',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the value of 2³?',
        'options' => ['6', '8', '9', '12'],
        'answer' => '8',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the slope of a line that is parallel to the x-axis?',
        'options' => ['0', '1', 'Undefined', '∞'],
        'answer' => '0',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the value of log₁₀ 1000?',
        'options' => ['1', '2', '3', '4'],
        'answer' => '3',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the solution to the equation x² = 9?',
        'options' => ['x = 3', 'x = ±3', 'x = 9', 'x = ±9'],
        'answer' => 'x = ±3',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the sum of the angles in a triangle?',
        'options' => ['180°', '90°', '360°', '270°'],
        'answer' => '180°',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the value of sin(30°)?',
        'options' => ['0.5', '1', '√2/2', '√3/2'],
        'answer' => '0.5',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the value of 3! (3 factorial)?',
        'options' => ['3', '6', '9', '12'],
        'answer' => '6',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the equation of a circle with a radius of 3 centered at the origin?',
        'options' => ['x² + y² = 9', 'x² + y² = 3', 'x² + y² = 6', 'x² + y² = 12'],
        'answer' => 'x² + y² = 9',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],

    // English - Extreme
    [
        'question' => 'Which of the following is a synonym of "gregarious"?',
        'options' => ['Shy', 'Reserved', 'Sociable', 'Introverted'],
        'answer' => 'Sociable',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'Which of the following sentences is grammatically correct?',
        'options' => ['He don\'t like coffee.', 'She doesn\'t likes tea.', 'He doesn\'t like coffee.', 'She don\'t likes tea.'],
        'answer' => 'He doesn\'t like coffee.',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the antonym of "benevolent"?',
        'options' => ['Malevolent', 'Kind', 'Generous', 'Compassionate'],
        'answer' => 'Malevolent',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'Identify the part of speech of the underlined word: "She quickly ran to the store."',
        'options' => ['Noun', 'Verb', 'Adverb', 'Adjective'],
        'answer' => 'Adverb',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'Which of the following words is a homophone?',
        'options' => ['Write', 'Rite', 'Right', 'All of the above'],
        'answer' => 'All of the above',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the meaning of the idiom "break the ice"?',
        'options' => ['To make someone laugh', 'To initiate a conversation', 'To stop talking', 'To confuse someone'],
        'answer' => 'To initiate a conversation',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the plural form of "child"?',
        'options' => ['Childs', 'Childes', 'Children', 'Childrens'],
        'answer' => 'Children',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the opposite of "optimistic"?',
        'options' => ['Pessimistic', 'Realistic', 'Idealistic', 'Cynical'],
        'answer' => 'Pessimistic',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the superlative form of the adjective "good"?',
        'options' => ['Goodest', 'Better', 'Best', 'Most good'],
        'answer' => 'Best',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'Choose the correct sentence:',
        'options' => ['I has two dogs.', 'I have two dog.', 'I has two dog.', 'I have two dogs.'],
        'answer' => 'I have two dogs.',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],

    // Physics - Extreme
    [
        'question' => 'What is the speed of light in a vacuum?',
        'options' => ['3 x 10⁸ m/s', '3 x 10⁶ m/s', '3 x 10⁵ m/s', '3 x 10⁷ m/s'],
        'answer' => '3 x 10⁸ m/s',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the unit of electric current?',
        'options' => ['Volt', 'Ohm', 'Watt', 'Ampere'],
        'answer' => 'Ampere',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the acceleration due to gravity on Earth?',
        'options' => ['9.8 m/s²', '8.9 m/s²', '10.2 m/s²', '9.6 m/s²'],
        'answer' => '9.8 m/s²',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the SI unit of power?',
        'options' => ['Joule', 'Watt', 'Newton', 'Volt'],
        'answer' => 'Watt',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'Which of the following is a non-renewable energy source?',
        'options' => ['Solar', 'Wind', 'Coal', 'Hydropower'],
        'answer' => 'Coal',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],

    // Chemistry - Extreme
    [
        'question' => 'What is the chemical formula for water?',
        'options' => ['H₂O', 'H₂O₂', 'HO₂', 'O₂'],
        'answer' => 'H₂O',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the atomic number of carbon?',
        'options' => ['12', '6', '8', '14'],
        'answer' => '6',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'Which element is represented by the symbol "Fe"?',
        'options' => ['Lead', 'Iron', 'Fluorine', 'Sodium'],
        'answer' => 'Iron',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What is the pH of a neutral solution?',
        'options' => ['0', '7', '14', '1'],
        'answer' => '7',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ],
    [
        'question' => 'What type of bond is formed between two hydrogen atoms?',
        'options' => ['Ionic', 'Covalent', 'Metallic', 'Hydrogen'],
        'answer' => 'Covalent',
        'question_type' => 'medium',
        'department' => 'Computer Science'
    ]
];



// SQL query with placeholders
$sql = "INSERT INTO exams (question, options, answer, question_type, department) 
        VALUES ($1, $2::text[], $3, $4, $5)";




// Iterate through each question and insert into the database
foreach ($questions as $question) {
    // Convert PHP array to PostgreSQL array format
    $options = '{' . implode(',', array_map(function ($item) {
        return '"' . pg_escape_string($item) . '"';
    }, $question['options'])) . '}';

    $params = array(
        $question['question'],
        $options,
        $question['answer'],
        $question['question_type'],
        $question['department']
    );

    // Execute the prepared statement
    $result =  pg_query_params($connection, $sql, $params);

    // Check execution result
    if (!$result) {
        echo "Insert failed: " . pg_last_error() . "\n";
    }
}

echo "Questions inserted successfully.";

// Close the connection
pg_close($connection);
