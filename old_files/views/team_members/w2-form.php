<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-2 Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000 !important;
        }
        .small-text {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container my-5 p-4 border">
        <div class="form-title">Form W-2 Wage and Tax Statement - 2025</div>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td colspan="3">a. Employee's Social Security Number</td>
                    <td>OMB No. 1545-0029</td>
                </tr>
                <tr>
                    <td colspan="3"><input type="text" class="form-control"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4">b. Employer Identification Number (EIN)</td>
                </tr>
                <tr>
                    <td colspan="4"><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="4">c. Employer's Name, Address, and ZIP Code</td>
                </tr>
                <tr>
                    <td colspan="4"><textarea class="form-control" rows="2"></textarea></td>
                </tr>
                <tr>
                    <td colspan="4">d. Control Number</td>
                </tr>
                <tr>
                    <td colspan="4"><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="2">e. Employee's First Name and Initial</td>
                    <td>Last Name</td>
                    <td>Suff.</td>
                </tr>
                <tr>
                    <td colspan="2"><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="4">f. Employee's Address and ZIP Code</td>
                </tr>
                <tr>
                    <td colspan="4"><textarea class="form-control" rows="2"></textarea></td>
                </tr>
                <tr>
                    <td>1. Wages, Tips, Other Compensation</td>
                    <td>2. Federal Income Tax Withheld</td>
                    <td>3. Social Security Wages</td>
                    <td>4. Social Security Tax Withheld</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td>5. Medicare Wages and Tips</td>
                    <td>6. Medicare Tax Withheld</td>
                    <td>7. Social Security Tips</td>
                    <td>8. Allocated Tips</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td>9.</td>
                    <td>10. Dependent Care Benefits</td>
                    <td colspan="2">11. Nonqualified Plans</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td colspan="2"><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        13. Statutory Employee<br>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"> Retirement Plan<br>
                            <input class="form-check-input" type="checkbox"> Third-Party Sick Pay
                        </div>
                    </td>
                    <td colspan="2">14. Other<br><textarea class="form-control" rows="2"></textarea></td>
                </tr>
                <tr>
                    <td>15. State Employer's State ID Number</td>
                    <td>16. State Wages, Tips, Etc.</td>
                    <td>17. State Income Tax</td>
                    <td>18. Local Wages, Tips, Etc.</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td>19. Local Income Tax</td>
                    <td>20. Locality Name</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control"></td>
                    <td><input type="text" class="form-control"></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
        <div class="small-text text-center mt-3">
            Department of the Treasury – Internal Revenue Service<br>
            Copy 1 – For State, City, or Local Tax Department
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
