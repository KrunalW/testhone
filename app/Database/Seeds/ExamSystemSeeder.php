<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExamSystemSeeder extends Seeder
{
    public function run()
    {
        // Insert Subjects
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Quantitative Aptitude and Mathematics'],
            ['name' => 'Logical Reasoning', 'code' => 'LOGIC', 'description' => 'Logical and Analytical Reasoning'],
            ['name' => 'English', 'code' => 'ENG', 'description' => 'English Language and Comprehension'],
            ['name' => 'General Knowledge', 'code' => 'GK', 'description' => 'General Knowledge and Current Affairs'],
            ['name' => 'General Science', 'code' => 'GS', 'description' => 'General Science'],
        ];

        foreach ($subjects as $subject) {
            $this->db->table('subjects')->insert($subject);
        }

        // Get subject IDs
        $mathId = $this->db->table('subjects')->where('code', 'MATH')->get()->getRow()->id;
        $logicId = $this->db->table('subjects')->where('code', 'LOGIC')->get()->getRow()->id;
        $engId = $this->db->table('subjects')->where('code', 'ENG')->get()->getRow()->id;
        $gkId = $this->db->table('subjects')->where('code', 'GK')->get()->getRow()->id;
        $gsId = $this->db->table('subjects')->where('code', 'GS')->get()->getRow()->id;

        // Create Sample Exam
        $exam = [
            'title' => 'SSC CGL Tier-1 Mock Test 2024',
            'description' => 'Combined Graduate Level Examination - Tier 1 Practice Test',
            'duration_minutes' => 60,
            'total_questions' => 50,
            'pass_percentage' => 40.00,
            'has_negative_marking' => 1,
            'negative_marks_per_question' => 0.25,
            'marks_per_question' => 1.00,
            'randomize_questions' => 1,
            'randomize_options' => 1,
            'prevent_tab_switch' => 1,
            'max_tab_switches_allowed' => 3,
            'status' => 'active',
        ];
        $this->db->table('exams')->insert($exam);
        $examId = $this->db->insertID();

        // Exam Subject Distribution
        $distributions = [
            ['exam_id' => $examId, 'subject_id' => $mathId, 'number_of_questions' => 10],
            ['exam_id' => $examId, 'subject_id' => $logicId, 'number_of_questions' => 10],
            ['exam_id' => $examId, 'subject_id' => $engId, 'number_of_questions' => 10],
            ['exam_id' => $examId, 'subject_id' => $gkId, 'number_of_questions' => 10],
            ['exam_id' => $examId, 'subject_id' => $gsId, 'number_of_questions' => 10],
        ];

        foreach ($distributions as $dist) {
            $this->db->table('exam_subject_distribution')->insert($dist);
        }

        // Mathematics Questions (10)
        $mathQuestions = [
            ['question' => 'If x + y = 10 and x - y = 4, what is the value of x?', 'correct' => 'C', 'options' => ['5', '6', '7', '8'], 'explanation' => 'Adding both equations: 2x = 14, x = 7'],
            ['question' => 'What is 15% of 200?', 'correct' => 'B', 'options' => ['25', '30', '35', '40'], 'explanation' => '15% of 200 = (15/100) × 200 = 30'],
            ['question' => 'The sum of three consecutive numbers is 72. What is the largest number?', 'correct' => 'D', 'options' => ['22', '23', '24', '25'], 'explanation' => 'Let numbers be x, x+1, x+2. Then 3x + 3 = 72, x = 23, largest = 25'],
            ['question' => 'If the area of a square is 64 sq cm, what is its perimeter?', 'correct' => 'C', 'options' => ['16 cm', '24 cm', '32 cm', '40 cm'], 'explanation' => 'Side = √64 = 8 cm, Perimeter = 4 × 8 = 32 cm'],
            ['question' => 'What is the value of (12)² - (8)²?', 'correct' => 'B', 'options' => ['60', '80', '100', '120'], 'explanation' => 'Using a² - b² = (a+b)(a-b): (12+8)(12-8) = 20 × 4 = 80'],
            ['question' => 'A train travels 120 km in 2 hours. What is its speed in km/h?', 'correct' => 'C', 'options' => ['50', '55', '60', '65'], 'explanation' => 'Speed = Distance/Time = 120/2 = 60 km/h'],
            ['question' => 'What is the average of 12, 18, 24, and 30?', 'correct' => 'B', 'options' => ['20', '21', '22', '23'], 'explanation' => 'Average = (12+18+24+30)/4 = 84/4 = 21'],
            ['question' => 'If a product costs Rs. 500 after a 20% discount, what was the original price?', 'correct' => 'D', 'options' => ['Rs. 600', 'Rs. 620', 'Rs. 625', 'Rs. 625'], 'explanation' => 'Original price = 500 × (100/80) = Rs. 625'],
            ['question' => 'What is 2³ × 2⁴?', 'correct' => 'A', 'options' => ['128', '64', '256', '512'], 'explanation' => '2³ × 2⁴ = 2⁽³⁺⁴⁾ = 2⁷ = 128'],
            ['question' => 'The ratio of boys to girls in a class is 3:2. If there are 15 boys, how many girls are there?', 'correct' => 'B', 'options' => ['8', '10', '12', '15'], 'explanation' => 'If 3 parts = 15, then 1 part = 5, so 2 parts (girls) = 10'],
        ];

        $this->insertQuestions($mathId, $mathQuestions);

        // Logical Reasoning Questions (10)
        $logicQuestions = [
            ['question' => 'Find the odd one out: 2, 4, 6, 9, 10', 'correct' => 'C', 'options' => ['2', '4', '9', '10'], 'explanation' => '9 is the only odd number'],
            ['question' => 'If BOOK is coded as CPPL, how is PAGE coded?', 'correct' => 'B', 'options' => ['QBHF', 'QBHG', 'QBIF', 'QCHF'], 'explanation' => 'Each letter is shifted by +1: P→Q, A→B, G→H, E→G'],
            ['question' => 'Complete the series: 2, 6, 12, 20, 30, ?', 'correct' => 'D', 'options' => ['36', '38', '40', '42'], 'explanation' => 'Difference increases by 2: +4, +6, +8, +10, +12 = 42'],
            ['question' => 'If A is the brother of B, and C is the father of A, what is C to B?', 'correct' => 'A', 'options' => ['Father', 'Uncle', 'Brother', 'Grandfather'], 'explanation' => 'C is the father of both A and B'],
            ['question' => 'Find the missing number: 3, 9, 27, ?, 243', 'correct' => 'C', 'options' => ['54', '72', '81', '108'], 'explanation' => 'Each number is multiplied by 3: 27 × 3 = 81'],
            ['question' => 'Which word does NOT belong: Apple, Banana, Carrot, Mango, Orange', 'correct' => 'C', 'options' => ['Apple', 'Banana', 'Carrot', 'Mango'], 'explanation' => 'Carrot is a vegetable, others are fruits'],
            ['question' => 'If South-East becomes North, what will North-West become?', 'correct' => 'B', 'options' => ['East', 'West', 'South', 'North-East'], 'explanation' => 'After 90° anti-clockwise rotation: NW becomes West'],
            ['question' => 'Clock shows 3:15. What is the angle between hour and minute hands?', 'correct' => 'A', 'options' => ['7.5°', '15°', '22.5°', '30°'], 'explanation' => 'Minute hand at 3 (90°), hour hand slightly past 3 (97.5°), difference = 7.5°'],
            ['question' => 'If + means ×, × means -, - means ÷, and ÷ means +, what is 8 + 2 - 4 × 3?', 'correct' => 'B', 'options' => ['3', '1', '5', '7'], 'explanation' => '8 × 2 ÷ 4 - 3 = 16 ÷ 4 - 3 = 4 - 3 = 1'],
            ['question' => 'In a certain code, Mumbai is written as NVNCBJ. How is DELHI written?', 'correct' => 'D', 'options' => ['EFMIJ', 'EFMIK', 'EFMII', 'EFMIJ'], 'explanation' => 'Each letter is shifted by +1: D→E, E→F, L→M, H→I, I→J'],
        ];

        $this->insertQuestions($logicId, $logicQuestions);

        // English Questions (10)
        $engQuestions = [
            ['question' => 'Choose the correct synonym of "Abundant":', 'correct' => 'B', 'options' => ['Scarce', 'Plentiful', 'Rare', 'Limited'], 'explanation' => 'Abundant means existing in large quantities, similar to Plentiful'],
            ['question' => 'Identify the antonym of "Brave":', 'correct' => 'C', 'options' => ['Bold', 'Fearless', 'Coward', 'Courageous'], 'explanation' => 'Brave means courageous, opposite is Coward'],
            ['question' => 'Fill in the blank: She _____ to the market every Sunday.', 'correct' => 'A', 'options' => ['goes', 'go', 'gone', 'going'], 'explanation' => 'Present simple tense with singular subject requires "goes"'],
            ['question' => 'Spot the error: "He don\'t like coffee."', 'correct' => 'B', 'options' => ['He', 'don\'t', 'like', 'No error'], 'explanation' => 'Should be "doesn\'t" with singular subject "He"'],
            ['question' => 'Choose the correctly spelled word:', 'correct' => 'D', 'options' => ['Acommodation', 'Accomodation', 'Acommodation', 'Accommodation'], 'explanation' => 'Accommodation has double C and double M'],
            ['question' => 'What is the plural of "Criterion"?', 'correct' => 'B', 'options' => ['Criterions', 'Criteria', 'Criterias', 'Criterion'], 'explanation' => 'Criterion is a Greek word, plural is Criteria'],
            ['question' => 'Identify the part of speech: "Quickly" in "He runs quickly"', 'correct' => 'C', 'options' => ['Noun', 'Verb', 'Adverb', 'Adjective'], 'explanation' => 'Quickly modifies the verb "runs", making it an adverb'],
            ['question' => 'Choose the correct passive voice: "She writes a letter."', 'correct' => 'A', 'options' => ['A letter is written by her', 'A letter was written by her', 'A letter written by her', 'A letter is writing by her'], 'explanation' => 'Present tense active becomes "is written" in passive'],
            ['question' => 'Meaning of idiom "Break the ice":', 'correct' => 'B', 'options' => ['To fight', 'To start a conversation', 'To break something', 'To stop talking'], 'explanation' => 'Break the ice means to initiate conversation in an awkward situation'],
            ['question' => 'Choose the correct article: "___ Himalayas are in India."', 'correct' => 'D', 'options' => ['A', 'An', 'No article', 'The'], 'explanation' => 'Mountain ranges take the definite article "The"'],
        ];

        $this->insertQuestions($engId, $engQuestions);

        // General Knowledge Questions (10)
        $gkQuestions = [
            ['question' => 'Who is known as the "Father of the Nation" in India?', 'correct' => 'A', 'options' => ['Mahatma Gandhi', 'Jawaharlal Nehru', 'Sardar Patel', 'B.R. Ambedkar'], 'explanation' => 'Mahatma Gandhi is honored as the Father of the Nation'],
            ['question' => 'What is the capital of Australia?', 'correct' => 'C', 'options' => ['Sydney', 'Melbourne', 'Canberra', 'Brisbane'], 'explanation' => 'Canberra is the capital city of Australia'],
            ['question' => 'In which year did India gain independence?', 'correct' => 'B', 'options' => ['1945', '1947', '1950', '1952'], 'explanation' => 'India gained independence on August 15, 1947'],
            ['question' => 'Who wrote "Romeo and Juliet"?', 'correct' => 'A', 'options' => ['William Shakespeare', 'Charles Dickens', 'Jane Austen', 'Mark Twain'], 'explanation' => 'Romeo and Juliet was written by William Shakespeare'],
            ['question' => 'Which is the largest ocean in the world?', 'correct' => 'D', 'options' => ['Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean', 'Pacific Ocean'], 'explanation' => 'Pacific Ocean is the largest and deepest ocean'],
            ['question' => 'Mount Everest is located in which mountain range?', 'correct' => 'B', 'options' => ['Alps', 'Himalayas', 'Rockies', 'Andes'], 'explanation' => 'Mount Everest is part of the Himalayan mountain range'],
            ['question' => 'Who was the first Prime Minister of India?', 'correct' => 'C', 'options' => ['Mahatma Gandhi', 'Sardar Patel', 'Jawaharlal Nehru', 'Lal Bahadur Shastri'], 'explanation' => 'Jawaharlal Nehru was India\'s first Prime Minister'],
            ['question' => 'Which country is known as the "Land of Rising Sun"?', 'correct' => 'A', 'options' => ['Japan', 'China', 'Thailand', 'South Korea'], 'explanation' => 'Japan is called the Land of the Rising Sun'],
            ['question' => 'What is the currency of United Kingdom?', 'correct' => 'B', 'options' => ['Dollar', 'Pound Sterling', 'Euro', 'Franc'], 'explanation' => 'The UK uses Pound Sterling as its currency'],
            ['question' => 'Who invented the telephone?', 'correct' => 'D', 'options' => ['Thomas Edison', 'Nikola Tesla', 'Albert Einstein', 'Alexander Graham Bell'], 'explanation' => 'Alexander Graham Bell invented the telephone in 1876'],
        ];

        $this->insertQuestions($gkId, $gkQuestions);

        // General Science Questions (10)
        $gsQuestions = [
            ['question' => 'What is the chemical symbol for water?', 'correct' => 'A', 'options' => ['H₂O', 'CO₂', 'O₂', 'H₂'], 'explanation' => 'Water is composed of two hydrogen atoms and one oxygen atom (H₂O)'],
            ['question' => 'Which planet is known as the Red Planet?', 'correct' => 'C', 'options' => ['Venus', 'Jupiter', 'Mars', 'Saturn'], 'explanation' => 'Mars appears red due to iron oxide on its surface'],
            ['question' => 'What is the powerhouse of the cell?', 'correct' => 'B', 'options' => ['Nucleus', 'Mitochondria', 'Ribosome', 'Chloroplast'], 'explanation' => 'Mitochondria produces energy (ATP) for the cell'],
            ['question' => 'What is the speed of light in vacuum?', 'correct' => 'D', 'options' => ['3 × 10⁶ m/s', '3 × 10⁷ m/s', '3 × 10⁹ m/s', '3 × 10⁸ m/s'], 'explanation' => 'Speed of light is approximately 300,000 km/s or 3 × 10⁸ m/s'],
            ['question' => 'Which gas is most abundant in Earth\'s atmosphere?', 'correct' => 'A', 'options' => ['Nitrogen', 'Oxygen', 'Carbon dioxide', 'Hydrogen'], 'explanation' => 'Nitrogen makes up about 78% of Earth\'s atmosphere'],
            ['question' => 'What is the pH value of pure water?', 'correct' => 'C', 'options' => ['5', '6', '7', '8'], 'explanation' => 'Pure water has a neutral pH of 7'],
            ['question' => 'Who developed the theory of relativity?', 'correct' => 'B', 'options' => ['Isaac Newton', 'Albert Einstein', 'Galileo Galilei', 'Stephen Hawking'], 'explanation' => 'Albert Einstein developed both special and general theories of relativity'],
            ['question' => 'What is the hardest natural substance on Earth?', 'correct' => 'D', 'options' => ['Gold', 'Iron', 'Platinum', 'Diamond'], 'explanation' => 'Diamond is the hardest known natural material'],
            ['question' => 'Which vitamin is produced when skin is exposed to sunlight?', 'correct' => 'C', 'options' => ['Vitamin A', 'Vitamin B', 'Vitamin D', 'Vitamin K'], 'explanation' => 'Skin produces Vitamin D when exposed to sunlight'],
            ['question' => 'What is the boiling point of water at sea level?', 'correct' => 'A', 'options' => ['100°C', '90°C', '110°C', '120°C'], 'explanation' => 'Water boils at 100°C (212°F) at sea level atmospheric pressure'],
        ];

        $this->insertQuestions($gsId, $gsQuestions);
    }

    private function insertQuestions($subjectId, $questions)
    {
        foreach ($questions as $index => $q) {
            $questionData = [
                'subject_id' => $subjectId,
                'question_text' => $q['question'],
                'explanation' => $q['explanation'],
                'difficulty_level' => 'medium',
            ];
            $this->db->table('questions')->insert($questionData);
            $questionId = $this->db->insertID();

            // Insert 4 options for each question
            $optionLabels = ['A', 'B', 'C', 'D'];
            foreach ($q['options'] as $optIndex => $optionText) {
                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => $optionText,
                    'is_correct' => ($optionLabels[$optIndex] === $q['correct']) ? 1 : 0,
                    'display_order' => $optIndex + 1,
                ];
                $this->db->table('options')->insert($optionData);
            }
        }
    }
}
