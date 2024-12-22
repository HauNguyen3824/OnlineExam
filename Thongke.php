
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #355e3b; /* Màu xanh rêu */
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #355e3b;
        }

        .stat-box {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
        }

        .stat {
            flex: 1;
            margin: 0 1rem;
            text-align: center;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #eaf4ea;
        }

        .stat h3 {
            margin: 0.5rem 0;
            color: #355e3b;
        }

        .chart {
            margin: 2rem 0;
        }

        .chart canvas {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            display: block;
        }

        footer {
            margin-top: 2rem;
            text-align: center;
            padding: 1rem;
            background-color: #355e3b;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        Thống Kê Đề Thi Trắc Nghiệm
    </header>

    <div class="container">
        <h2>Tổng Quan</h2>
        <div class="stat-box">
            <div class="stat">
                <h3>Số lượng đề thi</h3>
                <p>50</p>
            </div>
            <div class="stat">
                <h3>Số câu hỏi</h3>
                <p>500</p>
            </div>
            <div class="stat">
                <h3>Số lượt truy cập</h3>
                <p>10,000</p>
            </div>
        </div>

        <h2>Biểu Đồ Thống Kê</h2>
        <div class="chart">
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <footer>
        &copy;  Nhóm 6 OnlineExam
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Toán', 'Lý', 'Hóa', 'Sinh', 'Anh', 'Văn'],
                datasets: [{
                    label: 'Số lượng câu hỏi theo môn',
                    data: [120, 100, 90, 80, 110, 100],
                    backgroundColor: '#355e3b',
                    borderColor: '#2c4d31',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Thống Kê Câu Hỏi Theo Môn Học'
                    }
                }
            }
        });
    </script>
</body>
</html>
