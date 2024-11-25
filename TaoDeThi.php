<head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>OnlineExam</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
      
        
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
      
        <!-- Vendor CSS Files -->
        <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <link href="assets/vendor/aos/aos.css" rel="stylesheet">
        <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
        <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
      
        <!-- Main CSS File -->
        <link href="assets/css/main.css" rel="stylesheet">
      
        <!-- =======================================================
        * Template Name: Impact
        * Template URL: https://bootstrapmade.com/impact-bootstrap-business-website-template/
        * Updated: Aug 07 2024 with Bootstrap v5.3.3
        * Author: BootstrapMade.com
        * License: https://bootstrapmade.com/license/
        ======================================================== -->
      </head>
      
      <body class="index-page">
      
        <header id="header" class="header fixed-top">
      
          <div class="topbar d-flex align-items-center">
            <div class="container d-flex justify-content-center justify-content-md-between">
        
            </div>
          </div><!-- End Top Bar -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f0f9ff;
            --text-color: #1e293b;
            --border-color: #bfdbfe;
            --error-color: #ef4444;
        }

        /* body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        } */

        body>.container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        h1 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-row > * {
            flex: 2;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        select, input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        select:focus, input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .difficulty-section {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .difficulty-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .password-section {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        .error {
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .total-questions {
            text-align: right;
            font-weight: 500;
            margin-top: 0.5rem;
            color: var(--primary-color);
        }
    </style>

	<div class="section" id="content">
		<div class="container">
		<h1>Tạo Đề Thi</h1>
        <form id="examForm" onsubmit="handleSubmit(event)">
            <div class="form-row">
                <div class="form-group">
                    <label for="examName">Tên đề thi:</label>
                    <input type="text" id="examName" name="examName" required>
                </div>
                <div class="form-group">
                    <label for="school">Trường:</label>
                    <input type="text" id="school" name="school" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="subject">Môn học:</label>
                    <select id="subject" name="subject" required>
                        <option value="">-- Chọn môn học --</option>
                        <option value="toan">Toán</option>
                        <option value="ly">Vật lý</option>
                        <option value="hoa">Hóa học</option>
                        <option value="sinh">Sinh học</option>
                        <option value="anh">Tiếng Anh</option>
                        <option value="van">Ngữ văn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="duration">Thời gian làm bài (phút):</label>
                    <input type="number" id="duration" name="duration" min="15" max="180" value="60" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="examDate">Ngày làm bài:</label>
                    <input type="datetime-local" id="examDate" name="examDate" required>
                </div>
                <div class="form-group">
                    <label for="questionBank">Thư viện đề thi:</label>
                    <select id="questionBank" name="questionBank" required>
                        <option value="">-- Chọn thư viện --</option>
                        <option value="bank1">Đề thi THPT Quốc gia</option>
                        <option value="bank2">Đề thi học kì</option>
                        <option value="bank3">Đề thi thử nghiệm</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="totalQuestions">Tổng số câu hỏi:</label>
                <input type="number" id="totalQuestions" name="totalQuestions" min="1" max="100" value="40" required onchange="updateDifficultyLimits()">
            </div>

            <div class="difficulty-section">
                <div class="difficulty-title">Phân bố độ khó</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="easyQuestions">Số câu dễ:</label>
                        <input type="number" id="easyQuestions" name="easyQuestions" min="0" value="16" onchange="validateDifficultyDistribution()">
                    </div>
                    <div class="form-group">
                        <label for="mediumQuestions">Số câu trung bình:</label>
                        <input type="number" id="mediumQuestions" name="mediumQuestions" min="0" value="16" onchange="validateDifficultyDistribution()">
                    </div>
                    <div class="form-group">
                        <label for="hardQuestions">Số câu khó:</label>
                        <input type="number" id="hardQuestions" name="hardQuestions" min="0" value="8" onchange="validateDifficultyDistribution()">
                    </div>
                </div>
                <div class="total-questions" id="questionDistributionTotal" max-value="" value="">
                    Tổng: 40/40 câu
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="examMode">Chế độ tạo:</label>
                    <select id="examMode" name="examMode" required onchange="togglePasswordField()">
                        <option value="contest">Contest</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                <div class="form-group password-section" id="passwordSection">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password">
                </div>
            </div>

            <button type="submit" class="btn">Tạo đề thi</button>
        </form>
		</div>
	</div>
    <script src="./js/createExam.js"></script>

	<?php
		include 'footer.php';

	?>
</html>
