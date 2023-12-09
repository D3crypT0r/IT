<!-- index.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Visualization</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Student Data Visualization</h1>

    <?php
        require 'vendor/autoload.php';
        use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
        use PhpOffice\PhpSpreadsheet\IOFactory;

        function processData($filename, $filetype) {
            $data = [];
            switch ($filetype) {
                case 'csv':
                    if (($handle = fopen($filename, 'r')) !== false) {
                        $header = fgetcsv($handle, 1000, ',');
                        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                            $program = $row[10];
                            if (!isset($data[$program])) {
                                $data[$program] = 1;
                            } else {
                                $data[$program]++;
                            }
                        }
                        fclose($handle);
                    }
                    break;

                case 'xlsx':
                    $spreadsheet = IOFactory::load($filename);
                    $sheetData = $spreadsheet->getActiveSheet()->toArray();
                    $header = $sheetData[0];
                    array_shift($sheetData); // Remove header row
                    foreach ($sheetData as $row) {
                        $program = $row[10];
                        if (!isset($data[$program])) {
                            $data[$program] = 1;
                        } else {
                            $data[$program]++;
                        }
                    }
                    break;

                case 'json':
                    $jsonData = file_get_contents($filename);
                    $jsonArray = json_decode($jsonData, true);
                    foreach ($jsonArray as $item) {
                        $program = $item['Program_Name'];
                        if (!isset($data[$program])) {
                            $data[$program] = 1;
                        } else {
                            $data[$program]++;
                        }
                    }
                    break;

                default:
                    break;
            }

            return $data;
        }

        // Process data from different files
        $processedDataCSV = processData('data.csv', 'csv');
        $processedDataXLSX = processData('data.xlsx', 'xlsx');
        $processedDataJSON = processData('data.json', 'json');
    ?>

    <canvas id="programChartCSV" width="400" height="200"></canvas>
    <canvas id="programChartXLSX" width="400" height="200"></canvas>
    <canvas id="programChartJSON" width="400" height="200"></canvas>

    <script>
        
        var programDataCSV = <?php echo json_encode($processedDataCSV); ?>;
        var programDataXLSX = <?php echo json_encode($processedDataXLSX); ?>;
        var programDataJSON = <?php echo json_encode($processedDataJSON); ?>;
        
        var ctxCSV = document.getElementById('programChartCSV').getContext('2d');
        var chartCSV = new Chart(ctxCSV, {
            type: 'bar',
            data: {
                labels: Object.keys(programDataCSV),
                datasets: [{
                    label: 'Number of Students (CSV)',
                    data: Object.values(programDataCSV),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var ctxXLSX = document.getElementById('programChartXLSX').getContext('2d');
        var chartXLSX = new Chart(ctxXLSX, {
            type: 'bar',
            data: {
                labels: Object.keys(programDataXLSX),
