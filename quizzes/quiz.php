<?php
session_start();
require_once '../config.php';
require_once '../functions.php';

$ip = $_GET['ip'] ?? $_SERVER['REMOTE_ADDR'];
$mac = $_GET['mac'] ?? '';

// Validate MAC address format
if (!empty($mac) && !validateMACAddress($mac)) {
    logAccess("INVALID_MAC_FORMAT", "Invalid MAC format provided: $mac", $ip, 'SECURITY');
    $mac = ''; // Clear invalid MAC
}

$_SESSION['ip'] = $ip;
$_SESSION['mac'] = $mac;

// Log quiz access
logAccess("QUIZ_STARTED", "IP: $ip | MAC: " . ($mac ?: 'NOT_PROVIDED'), $ip);

// Include the question bank

$questions = [
    [
        "question" => "What is the best way to create a strong password?",
        "options" => ["Use your birthdate", "Use 'password123'", "Use a mix of letters, numbers, and symbols", "Use your name repeated"],
        "answer" => "Use a mix of letters, numbers, and symbols"
    ],
    [
        "question" => "Which of the following is considered good netiquette?",
        "options" => ["Typing in ALL CAPS", "Using polite language online", "Spamming links", "Ignoring messages"],
        "answer" => "Using polite language online"
    ],
    [
        "question" => "Which action helps protect your personal information online?",
        "options" => ["Sharing passwords with friends", "Using public Wi-Fi for banking", "Enabling two-factor authentication", "Posting your address on social media"],
        "answer" => "Enabling two-factor authentication"
    ],
    [
        "question" => "What should you do if you receive a suspicious email?",
        "options" => ["Click the link to check it", "Forward it to everyone", "Delete or report it", "Reply asking for verification"],
        "answer" => "Delete or report it"
    ],
    [
        "question" => "What is cyberbullying?",
        "options" => ["Friendly teasing online", "Using the internet to harass someone", "Posting educational content", "Sharing funny memes"],
        "answer" => "Using the internet to harass someone"
    ],
    [
        "question" => "What is an appropriate online behavior in group chats?",
        "options" => ["Interrupting others constantly", "Respecting everyone's turn to speak", "Posting unrelated ads", "Mocking others' opinions"],
        "answer" => "Respecting everyone's turn to speak"
    ],
    [
        "question" => "Why should you avoid oversharing personal information online?",
        "options" => ["It can lead to identity theft", "It makes your profile look boring", "It helps others guess your password", "It improves your social media ranking"],
        "answer" => "It can lead to identity theft"
    ],
    [
        "question" => "Which of these is a sign of a secure website?",
        "options" => ["HTTP in the URL", "Padlock icon in the browser", "Many pop-up ads", "Requesting your password via email"],
        "answer" => "Padlock icon in the browser"
    ],
    [
        "question" => "What does it mean to 'cite sources' online?",
        "options" => ["Copying content without credit", "Giving credit to original authors", "Writing your opinion only", "Sharing a website link without context"],
        "answer" => "Giving credit to original authors"
    ],
    [
        "question" => "Which behavior is considered unethical online?",
        "options" => ["Respecting others' privacy", "Plagiarizing content", "Reporting inappropriate content", "Using strong passwords"],
        "answer" => "Plagiarizing content"
    ],
    [
        "question" => "If someone posts a hurtful comment online, what is the responsible action?",
        "options" => ["Respond with a mean comment", "Ignore and report if needed", "Share it publicly to shame them", "Block everyone"],
        "answer" => "Ignore and report if needed"
    ],
    [
        "question" => "What is phishing?",
        "options" => ["A type of online game", "Attempting to steal sensitive information through fake messages", "A way to send memes", "A coding technique"],
        "answer" => "Attempting to steal sensitive information through fake messages"
    ],
    [
        "question" => "Why should you avoid clicking unknown links?",
        "options" => ["It might lead to malware or viruses", "It increases your internet speed", "It is polite to ignore links", "It helps websites earn revenue"],
        "answer" => "It might lead to malware or viruses"
    ],
    [
        "question" => "Which of these is considered respectful behavior online?",
        "options" => ["Trolling other users", "Using polite and positive language", "Posting personal insults", "Spamming comments"],
        "answer" => "Using polite and positive language"
    ],
    [
        "question" => "Why is it important to verify information before sharing online?",
        "options" => ["To avoid spreading false information", "To make the post more interesting", "To confuse readers", "It is not important"],
        "answer" => "To avoid spreading false information"
    ],
    [
        "question" => "Which of the following is an example of safe social media use?",
        "options" => ["Accepting friend requests from strangers", "Keeping profiles private and sharing limited info", "Posting your location daily", "Sharing passwords with friends"],
        "answer" => "Keeping profiles private and sharing limited info"
    ],
    [
        "question" => "What does 'digital footprint' refer to?",
        "options" => ["Your online activity that can be tracked", "Your favorite websites", "The icons on your desktop", "Your offline hobbies"],
        "answer" => "Your online activity that can be tracked"
    ],
    [
        "question" => "Which is an example of cyber safety practice?",
        "options" => ["Reusing the same password everywhere", "Updating software regularly", "Ignoring software updates", "Clicking on pop-ups immediately"],
        "answer" => "Updating software regularly"
    ],
    [
        "question" => "When posting photos online, you should:",
        "options" => ["Ask permission if it includes others", "Post anything without concern", "Tag everyone without consent", "Post private family photos publicly"],
        "answer" => "Ask permission if it includes others"
    ],
    [
        "question" => "What is the responsible way to comment on others' posts?",
        "options" => ["Be rude if you disagree", "Provide constructive and polite feedback", "Ignore posts entirely", "Copy someone else's comment"],
        "answer" => "Provide constructive and polite feedback"
    ],
    [
        "question" => "What should you do if you see a fake news story online?",
        "options" => ["Share it immediately", "Check facts before sharing", "Ignore it completely", "Rewrite it in your words"],
        "answer" => "Check facts before sharing"
    ],
    [
        "question" => "Which action is considered spamming?",
        "options" => ["Sending multiple unsolicited messages", "Posting a helpful link", "Commenting once politely", "Liking posts occasionally"],
        "answer" => "Sending multiple unsolicited messages"
    ],
    [
        "question" => "Why is it important to respect copyright online?",
        "options" => ["To avoid legal issues and respect creators", "It is not important", "To make your posts popular", "To increase followers quickly"],
        "answer" => "To avoid legal issues and respect creators"
    ],
    [
        "question" => "Which of these is NOT a safe online practice?",
        "options" => ["Using strong passwords", "Sharing your login with friends", "Logging out after use", "Enabling two-factor authentication"],
        "answer" => "Sharing your login with friends"
    ],
    [
        "question" => "What is the best way to handle online arguments?",
        "options" => ["Respond aggressively", "Ignore or discuss calmly", "Post negative memes", "Report everyone involved"],
        "answer" => "Ignore or discuss calmly"
    ],
    [
        "question" => "Why should you avoid using public computers for sensitive activities?",
        "options" => ["Your information can be stolen", "They are slow", "They provide better privacy", "They are always safe"],
        "answer" => "Your information can be stolen"
    ],
    [
        "question" => "Which statement reflects responsible internet use?",
        "options" => ["Sharing rumors without checking", "Respecting privacy and security", "Copying others’ work", "Posting personal attacks"],
        "answer" => "Respecting privacy and security"
    ],
    [
        "question" => "How can you prevent cyberbullying?",
        "options" => ["Ignore and block bullies, report when necessary", "Join in the bullying", "Share bullying posts", "Create fake accounts to retaliate"],
        "answer" => "Ignore and block bullies, report when necessary"
    ],
    [
        "question" => "Why is netiquette important?",
        "options" => ["It ensures polite and respectful online interactions", "It is only for schools", "It makes people follow you", "It prevents everyone from posting"],
        "answer" => "It ensures polite and respectful online interactions"
    ],
    [
        "question" => "What should you do before downloading files from the internet?",
        "options" => ["Check for malware and legitimacy", "Download anything quickly", "Ignore the source", "Ask a friend to download for you"],
        "answer" => "Check for malware and legitimacy"
    ],
    [
        "question" => "What is the risk of sharing personal photos publicly?",
        "options" => ["They can be misused by others", "They improve security", "It reduces your digital footprint", "It increases internet speed"],
        "answer" => "They can be misused by others"
    ],
    [
        "question" => "Which is considered ethical online behavior?",
        "options" => ["Respecting copyright", "Hacking into accounts", "Spreading rumors", "Ignoring privacy settings"],
        "answer" => "Respecting copyright"
    ],
    [
        "question" => "When chatting online, you should avoid:",
        "options" => ["Using polite language", "Sharing too much personal info", "Being honest", "Respecting others"],
        "answer" => "Sharing too much personal info"
    ],
    [
        "question" => "What is an example of safe browsing?",
        "options" => ["Visiting HTTPS websites only", "Clicking random pop-ups", "Disabling antivirus", "Sharing passwords"],
        "answer" => "Visiting HTTPS websites only"
    ],
    [
        "question" => "Why should you think before posting online?",
        "options" => ["Because posts can be permanent", "It is not necessary", "It decreases followers", "It is slow to post"],
        "answer" => "Because posts can be permanent"
    ],
    [
        "question" => "Which is a sign of responsible internet use?",
        "options" => ["Respecting others’ opinions", "Trolling strangers", "Posting inappropriate content", "Ignoring security warnings"],
        "answer" => "Respecting others’ opinions"
    ],
    [
        "question" => "What is the proper way to reply to an email professionally?",
        "options" => ["Using polite greetings and clear messages", "Using slang", "Writing only emojis", "Ignoring the message"],
        "answer" => "Using polite greetings and clear messages"
    ],
    [
        "question" => "Why is avoiding plagiarism important online?",
        "options" => ["To respect original authors and avoid academic dishonesty", "It is not important", "It increases your views", "It improves grammar automatically"],
        "answer" => "To respect original authors and avoid academic dishonesty"
    ],
    [
        "question" => "How can you ensure your online accounts remain secure?",
        "options" => ["Use strong, unique passwords and enable 2FA", "Use the same password for all accounts", "Share passwords with friends", "Ignore security updates"],
        "answer" => "Use strong, unique passwords and enable 2FA"
    ],
    [
        "question" => "Which of these reflects responsible social media use?",
        "options" => ["Thinking before posting", "Sharing unverified news", "Cyberbullying others", "Posting passwords publicly"],
        "answer" => "Thinking before posting"
    ],
    [
        "question" => "What is the main goal of netiquette?",
        "options" => ["Promote respectful and positive communication online", "Stop people from posting", "Increase followers only", "Make social media fun only"],
        "answer" => "Promote respectful and positive communication online"
    ],
    [
        "question" => "If you find copyrighted content online, you should:",
        "options" => ["Not copy without permission", "Copy and claim as yours", "Share it freely without credit", "Ignore copyright laws"],
        "answer" => "Not copy without permission"
    ],
    [
        "question" => "What should you do if you accidentally offend someone online?",
        "options" => ["Apologize sincerely", "Ignore them", "Mock them back", "Delete your account"],
        "answer" => "Apologize sincerely"
    ],
    [
        "question" => "Which is considered safe communication online?",
        "options" => ["Sharing sensitive info carefully with trusted contacts", "Posting private info publicly", "Talking to strangers without caution", "Forwarding passwords to others"],
        "answer" => "Sharing sensitive info carefully with trusted contacts"
    ],
    [
        "question" => "Why should you limit screen time and internet use responsibly?",
        "options" => ["To maintain balance and health", "It is unnecessary", "To spend more time online", "To ignore offline responsibilities"],
        "answer" => "To maintain balance and health"
    ],
    [
        "question" => "Which of the following is an example of phishing?",
        "options" => ["Fake bank emails asking for your password", "A friend sending a funny meme", "Official company newsletter", "School announcement email"],
        "answer" => "Fake bank emails asking for your password"
    ]
];

// Select 5 random questions for this session
if (!isset($_SESSION['quiz_questions'])) {
    shuffle($questions);
    $_SESSION['quiz_questions'] = array_slice($questions, 0, QUIZ_QUESTIONS_COUNT);
    $_SESSION['current'] = 0;
    $_SESSION['score'] = 0;
}

$current = $_SESSION['current'];
$totalQuestions = count($_SESSION['quiz_questions']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['answer'] ?? '';

    // Check answer
    if ($selected === $_SESSION['quiz_questions'][$current]['answer']) {
        $_SESSION['score']++;
    }

    $_SESSION['current']++;

    if ($_SESSION['current'] >= $totalQuestions) {
        // Calculate pass based on percentage threshold
        $required_correct = ceil($totalQuestions * QUIZ_PASS_PERCENTAGE);
        $passed = ($_SESSION['score'] >= $required_correct) ? 1 : 0;
        
        $score = $_SESSION['score'];
        $total = $totalQuestions;
        
        logAccess("QUIZ_COMPLETED", "Score: $score/$total (Required: $required_correct)", $ip);
        
        session_destroy();
        header("Location: process.php?passed=" . $passed . "&score=" . $score . "&total=" . $total);
        exit;
    } else {
        $current = $_SESSION['current'];
    }
}

$question = $_SESSION['quiz_questions'][$current];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internet Advocacy Quiz - ARALINKS</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .outer-box {
            max-width: 700px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
            color: black;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #0056b3);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-text {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Inner question box */
        .question-box {
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            border: 2px solid #e9ecef;
            margin-bottom: 20px;
        }

        .question-text {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* Options inside inner question box */
        .option-label {
            display: block;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #007bff;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            user-select: none;
            background-color: #fff;
            font-size: 16px;
            line-height: 1.4;
        }

        .option-label:hover {
            background-color: #e7f0ff;
            border-color: #0056b3;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"]:checked + .option-label {
            border-color: #007bff;
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        h3 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #1a1a1a;
            font-size: 22px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .outer-box {
                margin: 20px auto;
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .outer-box {
                margin: 15px auto;
                padding: 15px;
            }

            h3 {
                font-size: 18px;
            }

            .question-text {
                font-size: 16px;
            }

            .option-label {
                padding: 12px;
                font-size: 14px;
                margin: 8px 0;
            }

            .btn-submit {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
<div class="outer-box">
    <h3>📚 Internet Advocacy Quiz</h3>
    <p class="subtitle">Answer correctly to gain free WiFi access</p>

    <div class="progress-text">Question <?php echo $current + 1; ?> of <?php echo $totalQuestions; ?></div>
    <div class="progress-bar">
        <div class="progress-fill" style="width: <?php echo (($current + 1) / $totalQuestions) * 100; ?>%"></div>
    </div>

    <form method="post">
        <!-- Inner Question Box -->
        <div class="question-box">
            <p class="question-text"><?php echo htmlspecialchars($question['question']); ?></p>

            <?php foreach ($question['options'] as $index => $option): ?>
                <input type="radio" id="option<?php echo $index; ?>" name="answer" value="<?php echo htmlspecialchars($option); ?>" required>
                <label class="option-label" for="option<?php echo $index; ?>">
                    <?php echo htmlspecialchars($option); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn-submit">Next Question</button>
    </form>

    <div class="back-link">
        <a href="../index.php">← Cancel Quiz</a>
    </div>
</div>

</body>
</html>