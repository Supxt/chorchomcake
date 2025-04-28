<?php
session_start();
include('dbconnect.php');
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>

    <style>
        body {

            background: linear-gradient(to right, #d79577, rgb(240, 219, 227));
            font-family: 'Arial', sans-serif;
            background-color: rgb(240, 126, 126);
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 50;
            height: 100%;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 0px;
        }

        .form-group label {
            font-size: 14px;
            color: #555;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 16px;
            outline: none;
        }


        .form-group input:focus {
            border-color: #d79577;
        }

        .form-group input[type="submit"] {
            background-color: #d79577;
            color: white;
            cursor: pointer;
            border: none;
        }

        .form-group input[type="submit"]:hover {
            background-color: #d79577;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 16px;
            outline: none;
        }

        .form-group select:focus {
            border-color: #d79577;
        }

        .form-group select [type="submit"] {
            background-color: rgb(255, 255, 255);
            color: white;
            cursor: pointer;
            border: 10px;
        }

        .form-group select [type="submit"]:hover {
            background-color: #d79577;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .footer a {
            color: #d79577;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .topnav a.active {
            background-color: rgb(141, 85, 68);
            color: white;
        }

        .error {
            width: 92%;
            margin: 0px auto;
            padding: 10px;
            border: 1px solid #a94442;
            color: #a94442;
            background: #f2dede;
            border-radius: 5px;
            text-align: left;
        }

        .success {
            color: #3c763d;
            background: #dff0d8;
            border: 1px solid #3c763d;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>สมัครสมาชิก</h2>
        <form id="register-form" action="save_register.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="email">*อีเมล</label>
                <input required type="email" name="email" placeholder="กรุณาระบุ" onchange="validateEmail(this)" />
                <small class="error-msg" style="color:red;display:none;">รูปแบบอีเมลไม่ถูกต้อง</small>
            </div>

            <div class="form-group">
                <label for="password">*รหัสผ่าน</label>
                <input required type="password" name="password" placeholder="กรุณาระบุ" onchange="checkPasswords()" />
            </div>

            <div class="form-group">
                <label for="confirmpassword">*ยืนยันรหัสผ่าน</label>
                <input required type="password" name="confirmpassword" placeholder="กรุณาระบุ" onchange="checkPasswords()" />
                <small class="error-msg" id="password-error" style="color:red;display:none;">รหัสผ่านไม่ตรงกัน</small>
            </div>

            <div class="form-group">
                <label for="firstname">*ชื่อ</label>
                <input required type="text" name="firstname" placeholder="กรุณาระบุ" />
            </div>

            <div class="form-group">
                <label for="lastname">*นามสกุล</label>
                <input required type="text" name="lastname" placeholder="กรุณาระบุ" />
            </div>

            <div class="form-group">
                <label for="tel">*เบอร์โทรศัพท์</label>
                <input required type="text" name="tel" placeholder="กรุณากรอกเบอร์โทรศัพท์" onchange="validatePhone(this)" />
                <small class="error-msg" style="color:red;display:none;">เบอร์โทรศัพท์ไม่ถูกต้อง</small>
            </div>

            <div class="form-group">
                <label for="address">*ที่อยู่</label>
                <input required type="text" name="address" placeholder="กรุณากรอกที่อยู่" />
            </div>

            <div class="form-group">
                <label for="province">*จังหวัด</label>
                <select required name="province" id="province-select">
                    <option value="">-- กรุณาเลือกจังหวัด --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="district">*เขต/อำเภอ</label>
                <select required name="district" id="district-select">
                    <option value="">-- กรุณาเลือกอำเภอ --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sub_district">*แขวง/ตำบล</label>
                <select required name="sub_district" id="sub-district-select">
                    <option value="">-- กรุณาเลือกตำบล --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="post_code">*รหัสไปรษณีย์</label>
                <select required name="post_code" id="postcode-select">
                    <option value="">-- รหัสไปรษณีย์ --</option>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" name="reg_user" value="สมัครสมาชิก" />
            </div>
        </form>

        <script>
            function validateEmail(input) {
                const email = input.value;
                const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const errorMsg = input.nextElementSibling;
                if (!pattern.test(email)) {
                    errorMsg.style.display = "block";
                } else {
                    errorMsg.style.display = "none";
                }
            }

            function validatePhone(input) {
                const phone = input.value;
                const pattern = /^0[0-9]{8,9}$/;
                const errorMsg = input.nextElementSibling;
                if (!pattern.test(phone)) {
                    errorMsg.style.display = "block";
                } else {
                    errorMsg.style.display = "none";
                }
            }

            function checkPasswords() {
                const pass = document.querySelector('input[name="password"]');
                const confirm = document.querySelector('input[name="confirmpassword"]');
                const error = document.getElementById('password-error');
                if (pass.value && confirm.value && pass.value !== confirm.value) {
                    error.style.display = 'block';
                } else {
                    error.style.display = 'none';
                }
            }

            function validateForm() {
                const email = document.querySelector('input[name="email"]');
                const tel = document.querySelector('input[name="tel"]');
                const pass = document.querySelector('input[name="password"]');
                const confirm = document.querySelector('input[name="confirmpassword"]');
                const errorMsgs = document.querySelectorAll(".error-msg");

                // Trigger all validations
                validateEmail(email);
                validatePhone(tel);
                checkPasswords();

                // Check if any error is visible
                for (const msg of errorMsgs) {
                    if (msg.style.display === "block") {
                        alert("กรุณาแก้ไขข้อมูลที่ไม่ถูกต้องก่อนส่งฟอร์ม");
                        return false;
                    }
                }

                return true;
            }
        </script>


        <div class="footer">
            <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>

        </div>
    </div>
    <script>
        let provinceData = [];

        // โหลดข้อมูลจาก API
        fetch("https://raw.githubusercontent.com/kongvut/thai-province-data/master/api_province_with_amphure_tambon.json")
            .then(res => res.json())
            .then(data => {
                provinceData = data;
                const provinceSelect = document.getElementById('province-select');
                data.forEach(province => {
                    const opt = document.createElement('option');
                    opt.value = province.name_th;
                    opt.textContent = province.name_th;
                    provinceSelect.appendChild(opt);
                });
            });

        document.getElementById('province-select').addEventListener('change', function() {
            const selectedProvince = this.value;
            console.log("คุณเลือกจังหวัด:", selectedProvince);

            // ล้าง dropdown อื่น ๆ
            clearDropdown('district-select');
            clearDropdown('sub-district-select');
            clearDropdown('postcode-select');

            const province = provinceData.find(p => p.name_th === selectedProvince);
            if (province) {
                const districtSelect = document.getElementById('district-select');
                province.amphure.forEach(amphure => {
                    const opt = document.createElement('option');
                    opt.value = amphure.name_th;
                    opt.textContent = amphure.name_th;
                    districtSelect.appendChild(opt);
                });
            }
        });

        document.getElementById('district-select').addEventListener('change', function() {
            const selectedProvince = document.getElementById('province-select').value;
            const selectedDistrict = this.value;
            console.log("คุณเลือกอำเภอ:", selectedDistrict);

            clearDropdown('sub-district-select');
            clearDropdown('postcode-select');

            const province = provinceData.find(p => p.name_th === selectedProvince);
            if (province) {
                const amphure = province.amphure.find(a => a.name_th === selectedDistrict);
                if (amphure) {
                    const subDistrictSelect = document.getElementById('sub-district-select');
                    amphure.tambon.forEach(tambon => {
                        const opt = document.createElement('option');
                        opt.value = tambon.name_th;
                        opt.textContent = tambon.name_th;
                        subDistrictSelect.appendChild(opt);
                    });
                }
            }
        });

        document.getElementById('sub-district-select').addEventListener('change', function() {
            const selectedProvince = document.getElementById('province-select').value;
            const selectedDistrict = document.getElementById('district-select').value;
            const selectedSub = this.value;
            console.log("คุณเลือกตำบล:", selectedSub);

            clearDropdown('postcode-select');

            const province = provinceData.find(p => p.name_th === selectedProvince);
            if (province) {
                const amphure = province.amphure.find(a => a.name_th === selectedDistrict);
                if (amphure) {
                    const tambon = amphure.tambon.find(t => t.name_th === selectedSub);
                    if (tambon) {
                        const postcodeSelect = document.getElementById('postcode-select');
                        const opt = document.createElement('option');
                        opt.value = tambon.zip_code;
                        opt.textContent = tambon.zip_code;
                        postcodeSelect.appendChild(opt);
                        console.log("รหัสไปรษณีย์:", tambon.zip_code);
                    }
                }
            }
        });

        function clearDropdown(id) {
            const select = document.getElementById(id);
            select.innerHTML = '<option value="">-- กรุณาเลือก --</option>';
        }
    </script>
</body>

</html>