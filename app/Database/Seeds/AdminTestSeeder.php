<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminTestSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        echo "Creating test subjects...\n";

        // Insert subjects
        $subjects = [
            [
                'code' => 'MATH',
                'name' => 'Mathematics',
                'description' => 'Basic mathematics concepts including algebra and geometry',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'ENG',
                'name' => 'English',
                'description' => 'English language, grammar and comprehension',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'SCI',
                'name' => 'Science',
                'description' => 'General science including physics, chemistry and biology',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'GK',
                'name' => 'General Knowledge',
                'description' => 'Current affairs and general awareness',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($subjects as $subject) {
            $db->table('subjects')->insert($subject);
        }

        echo "Subjects created successfully!\n";
        echo "Getting subject IDs...\n";

        // Get subject IDs
        $mathId = $db->table('subjects')->where('code', 'MATH')->get()->getRow()->id;
        $engId = $db->table('subjects')->where('code', 'ENG')->get()->getRow()->id;
        $sciId = $db->table('subjects')->where('code', 'SCI')->get()->getRow()->id;
        $gkId = $db->table('subjects')->where('code', 'GK')->get()->getRow()->id;

        echo "Creating test questions...\n";

        // Mathematics Questions
        $mathQuestions = [
            [
                'question_text' => 'What is the value of 15 + 25?',
                'options' => ['30', '40', '50', '35'],
                'correct' => 1 // Index of correct answer (40)
            ],
            [
                'question_text' => 'What is the square root of 144?',
                'options' => ['10', '11', '12', '13'],
                'correct' => 2 // 12
            ],
            [
                'question_text' => 'What is 25% of 200?',
                'options' => ['25', '50', '75', '100'],
                'correct' => 1 // 50
            ],
            [
                'question_text' => 'Solve: 5 × 6 + 10',
                'options' => ['20', '30', '40', '50'],
                'correct' => 2 // 40
            ],
            [
                'question_text' => 'What is the value of π (pi) approximately?',
                'options' => ['2.14', '3.14', '4.14', '5.14'],
                'correct' => 1 // 3.14
            ]
        ];

        $this->insertQuestions($db, $mathId, $mathQuestions);

        // English Questions
        $engQuestions = [
            [
                'question_text' => 'Choose the correct spelling:',
                'options' => ['Recieve', 'Receive', 'Recive', 'Receeve'],
                'correct' => 1
            ],
            [
                'question_text' => 'What is the plural of "child"?',
                'options' => ['Childs', 'Children', 'Childes', 'Childrens'],
                'correct' => 1
            ],
            [
                'question_text' => 'Identify the noun in: "The quick brown fox jumps"',
                'options' => ['quick', 'brown', 'fox', 'jumps'],
                'correct' => 2
            ],
            [
                'question_text' => 'Choose the antonym of "happy":',
                'options' => ['Joyful', 'Sad', 'Excited', 'Cheerful'],
                'correct' => 1
            ],
            [
                'question_text' => 'Which is a verb?',
                'options' => ['Beautiful', 'Running', 'Happiness', 'Quickly'],
                'correct' => 1
            ]
        ];

        $this->insertQuestions($db, $engId, $engQuestions);

        // Science Questions
        $sciQuestions = [
            [
                'question_text' => 'What is the chemical symbol for water?',
                'options' => ['H2O', 'CO2', 'O2', 'N2'],
                'correct' => 0
            ],
            [
                'question_text' => 'Which planet is known as the Red Planet?',
                'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                'correct' => 1
            ],
            [
                'question_text' => 'How many bones are in the human body?',
                'options' => ['106', '206', '306', '406'],
                'correct' => 1
            ],
            [
                'question_text' => 'What is the speed of light?',
                'options' => ['3×10⁵ km/s', '3×10⁶ km/s', '3×10⁷ km/s', '3×10⁸ m/s'],
                'correct' => 3
            ],
            [
                'question_text' => 'Which gas do plants absorb from the atmosphere?',
                'options' => ['Oxygen', 'Nitrogen', 'Carbon Dioxide', 'Hydrogen'],
                'correct' => 2
            ]
        ];

        $this->insertQuestions($db, $sciId, $sciQuestions);

        // General Knowledge Questions
        $gkQuestions = [
            [
                'question_text' => 'Who is known as the Father of the Nation in India?',
                'options' => ['Jawaharlal Nehru', 'Mahatma Gandhi', 'Sardar Patel', 'Subhas Chandra Bose'],
                'correct' => 1
            ],
            [
                'question_text' => 'What is the capital of India?',
                'options' => ['Mumbai', 'Kolkata', 'New Delhi', 'Chennai'],
                'correct' => 2
            ],
            [
                'question_text' => 'Which is the longest river in the world?',
                'options' => ['Amazon', 'Nile', 'Ganges', 'Mississippi'],
                'correct' => 1
            ],
            [
                'question_text' => 'In which year did India gain independence?',
                'options' => ['1945', '1946', '1947', '1948'],
                'correct' => 2
            ],
            [
                'question_text' => 'Who wrote "Romeo and Juliet"?',
                'options' => ['Charles Dickens', 'William Shakespeare', 'Jane Austen', 'Mark Twain'],
                'correct' => 1
            ]
        ];

        $this->insertQuestions($db, $gkId, $gkQuestions);

        echo "Test questions created successfully!\n";
        echo "\nSummary:\n";
        echo "- 4 Subjects created\n";
        echo "- 20 Questions created (5 per subject)\n";
        echo "- 80 Options created (4 per question)\n";
        echo "\nYou can now create exams using these subjects and questions!\n";
    }

    private function insertQuestions($db, $subjectId, $questions)
    {
        foreach ($questions as $q) {
            // Insert question
            $questionData = [
                'subject_id' => $subjectId,
                'question_text' => $q['question_text'],
                'question_type' => 'text',
                'question_image_path' => null,
                'explanation' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('questions')->insert($questionData);
            $questionId = $db->insertID();

            // Insert options
            foreach ($q['options'] as $index => $optionText) {
                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => $optionText,
                    'option_image_path' => null,
                    'is_correct' => ($index === $q['correct']) ? 1 : 0,
                    'display_order' => $index + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $db->table('options')->insert($optionData);
            }
        }
    }
}
