<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Feedback Form</title>
    <!-- Bootstrap CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .form-header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            margin-top: 5px;
            padding-top: 15px;
            
            border-radius: 10px;
        }
        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .section:last-child {
            border-bottom: none;
        }
        .rating-options {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }
        .rating-table th {
            white-space: nowrap;
            vertical-align: middle;
        }
        .rating-table td {
            vertical-align: middle;
        }
        .form-footer {
            margin-top: 30px;
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="form-container bg-white">
            <div class="form-header" style="background-color:#1a3c5e; color: white;">
                <h1 class="h2">CLIENT'S FEEDBACK FORM</h1>
            </div>
            
            <form action="process_feedback.php" method="post">
                <!-- Personal Information Section -->
                <div class="section">
                    <h2 class="h4 mb-4">Personal Information</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Date of Visit</label>
                            <input type="date" class="form-control" id="visit_date" name="visit_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age" min="1" max="120">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sex</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sex" id="male" value="Male" required>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sex" id="female" value="Female">
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="region" class="form-label">Region</label>
                            <input type="text" class="form-control" id="region" name="region">
                        </div>
                    </div>
                </div>

                <!-- Office Information Section -->
                <div class="section">
                    <h2 class="h4 mb-4">Office Information</h2>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="office_id" class="form-label">Office Visited</label>
                            <select class="form-select" id="office_id" name="office_id" required>
                                <option value="" disabled selected>Select an office</option>
                                <?php
                                require_once 'config.php';
                                $result = $conn->query("SELECT office_id, office_name FROM offices");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value=\"{$row['office_id']}\">{$row['office_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label for="service_availed" class="form-label">Service availed</label>
                            <input type="text" class="form-control" id="service_availed" name="service_availed" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Community</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="community" id="faculty" value="Faculty/Staff" required>
                                    <label class="form-check-label" for="faculty">Faculty/Staff</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="community" id="student" value="Students">
                                    <label class="form-check-label" for="student">Student</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="community" id="visitor" value="Visitor">
                                    <label class="form-check-label" for="visitor">Visitor</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Citizen's Charter Feedback Section -->
                <div class="section">
                    <h2 class="h4 mb-4">Citizen's Charter Feedback</h2>
                    <p class="text-muted mb-4"><em>The Citizen's Charter is an official document that reflects the services of a government office including requirements, fees, & processing times among others</em></p>
                    
                    <div class="mb-4">
                        <h3 class="h5 mb-3">CCI. Which of the following best describes your awareness of a Citizen's Charter (CC)?</h3>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc1" id="cc1-1" value="1" required>
                            <label class="form-check-label" for="cc1-1">1. I know what a CC is and I saw this office's CC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc1" id="cc1-2" value="2">
                            <label class="form-check-label" for="cc1-2">2. I know what a CC is but I did NOT see this office's CC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc1" id="cc1-3" value="3">
                            <label class="form-check-label" for="cc1-3">3. I learned of the CC only when I saw this office's CC</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc1" id="cc1-4" value="4">
                            <label class="form-check-label" for="cc1-4">4. I do not know what a CC is and I did not see one</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h3 class="h5 mb-3">CC2. If aware of CC (answered 1-3 in CCI), would you say that the CC of this office was...</h3>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc2" id="cc2-1" value="1 - Easy to see">
                            <label class="form-check-label" for="cc2-1">1. Easy to see</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc2" id="cc2-2" value="2 - Somewhat easy to see">
                            <label class="form-check-label" for="cc2-2">2. Somewhat easy to see</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc2" id="cc2-3" value="3 - Difficult to see">
                            <label class="form-check-label" for="cc2-3">3. Difficult to see</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc2" id="cc2-4" value="4 - Not visible at all">
                            <label class="form-check-label" for="cc2-4">4. Not visible at all</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc2" id="cc2-5" value="5 - N/A">
                            <label class="form-check-label" for="cc2-5">5. N/A</label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="h5 mb-3">CC3. If aware of CC (answered codes 1-3 in CCI), how much did the CC help you in your transaction?</h3>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc3" id="cc3-1" value="1 - Helped very much">
                            <label class="form-check-label" for="cc3-1">1. Helped very much</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc3" id="cc3-2" value="2 - Somewhat helped">
                            <label class="form-check-label" for="cc3-2">2. Somewhat helped</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc3" id="cc3-3" value="3 - Did not help">
                            <label class="form-check-label" for="cc3-3">3. Did not help</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cc3" id="cc3-4" value="4 - N/A">
                            <label class="form-check-label" for="cc3-4">4. N/A</label>
                        </div>
                    </div>
                </div>

                <!-- Service Quality Dimensions Section -->
                <div class="section">
                    <h2 class="h4 mb-4">Service Quality Dimensions</h2>
                    <p class="text-muted mb-4"><em>INSTRUCTION: For SQD 0-8, please check appropriate circle on the column that best corresponds to your answer.</em></p>
                    
                    <div class="rating-options">
                        <span>Strongly Disagree (1)</span>
                        <span>Disagree (2)</span>
                        <span>Neutral (3)</span>
                        <span>Agree (4)</span>
                        <span>Strongly Agree (5)</span>
                        <span>Not Applicable (NA)</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered rating-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Question</th>
                                    <th class="text-center">1</th>
                                    <th class="text-center">2</th>
                                    <th class="text-center">3</th>
                                    <th class="text-center">4</th>
                                    <th class="text-center">5</th>
                                    <th class="text-center">N/A</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SQD0. I am satisfied with the service that I availed</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd0" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd0" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd0" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd0" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd0" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd0" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD1. I spent a reasonable amount of time for my transaction (Responsiveness)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd1" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd1" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd1" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd1" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd1" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd1" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD2. The office followed the transaction's requirements and steps based on information provided. (Reliability)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd2" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd2" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd2" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd2" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd2" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd2" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD3. The steps (including payment) I needed to do for my transaction were easy and simple. (Access and Facilities)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd3" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd3" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd3" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd3" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd3" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd3" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD4. I easily found information about my transaction from the office or its website (Communication)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd4" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd4" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd4" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd4" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd4" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd4" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD5. I paid a reasonable amount of fees for my transaction (Costs) (if the service was FREE, mark N/A column)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd5" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd5" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd5" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd5" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd5" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd5" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD6. I feel the office was fair to everyone, or "waiang palakasaan", during my transaction. (Integrity)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd6" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd6" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd6" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd6" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd6" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd6" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD7. I was treated courteously by the staff, and (if asked for help) the staff was helpful.(Assurance)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd7" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd7" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd7" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd7" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd7" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd7" value="NA"></td>
                                </tr>
                                <tr>
                                    <td>SQD8. I got what I needed from the government office or (if denied) denial of request was sufficiently explained to me.(Outcome)</td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd8" value="1" required></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd8" value="2"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd8" value="3"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd8" value="4"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd8" value="5"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="sqd8" value="NA"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Suggestions Section -->
                <div class="section">
                    <h2 class="h4 mb-3">Suggestions/Recommendations/Comments:</h2>
                    <h2 class="h4 mb-3">Comment Type:</h2>
                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="comment_type" id="good" value="Good" required>
                            <label class="form-check-label" for="good">Good</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="comment_type" id="bad" value="Bad">
                            <label class="form-check-label" for="bad">Bad</label>
                        </div>
                    </div>
                    
                    <textarea class="form-control" name="comments" rows="4" placeholder="Please provide any additional feedback..."></textarea>
                </div>

                <!-- Phone Number Section -->
                <div class="section">
                    <h2 class="h4 mb-3">Contact Information:</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Submit Feedback</button>
                </div>
            </form>

            <div class="form-footer mt-5">
                <p>Form Code: TAO-UP-QE-04 | Revision No.: 01 | Effectivity: December 19, 2024 | Page: 1 of 1</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set today's date as the default value for the date input
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('visit_date').value = today;
        });
    </script>
</html>