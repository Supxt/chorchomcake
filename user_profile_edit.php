<?php
include('dbconnect.php');

// ตรวจสอบว่าเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

// ดึงข้อมูลผู้ใช้จาก DB
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// ถ้าไม่พบข้อมูล
if (!$user) {
  echo "ไม่พบข้อมูลผู้ใช้";
  exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>แก้ไขข้อมูลผู้ใช้งาน</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="container" style="max-width: 500px; margin: 50px auto; padding: 30px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background-color: #fff;">
    <h2 style="text-align: center; margin-bottom: 20px;color:rgb(158, 107, 83)">แก้ไขข้อมูลผู้ใช้งาน</h2>
    <form action="edit_profile.php" method="POST">

      <label>อีเมล</label>
      <input type="email" name="email" disabled value="<?= htmlspecialchars($user['email']) ?>" required style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label>รหัสผ่านใหม่ (ถ้าต้องการเปลี่ยน)</label>
      <input type="password" name="password" placeholder="กรุณาระบุรหัสผ่านใหม่หรือเว้นว่างไว้" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label>ยืนยันรหัสผ่าน</label>
      <input type="password" name="confirmpassword" placeholder="ยืนยันรหัสผ่านใหม่" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label>ชื่อ</label>
      <input type="text" name="firstname" value="<?= htmlspecialchars($user['first_name']) ?>" required style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label>นามสกุล</label>
      <input type="text" name="lastname" value="<?= htmlspecialchars($user['last_name']) ?>" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label>เบอร์โทรศัพท์</label>
      <input type="text" name="tel" value="<?= htmlspecialchars($user['tel']) ?>" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label>ที่อยู่</label>
      <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">

      <label for="province">จังหวัด</label>
      <select name="province" id="province-select" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">
        <option value="">-- กรุณาเลือกจังหวัด --</option>
      </select>

      <label for="district">เขต/อำเภอ</label>
      <select name="district" id="district-select" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">
        <option value="">-- กรุณาเลือกอำเภอ --</option>
      </select>

      <label for="sub_district">แขวง/ตำบล</label>
      <select name="sub_district" id="sub-district-select" style="width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 10px;">
        <option value="">-- กรุณาเลือกตำบล --</option>
      </select>

      <label for="post_code">รหัสไปรษณีย์</label>
      <select name="post_code" id="postcode-select" style="width: 100%; margin-bottom: 20px; padding: 8px; border-radius: 10px;">
        <option value="">-- รหัสไปรษณีย์ --</option>
      </select>

      <input type="submit" name="update_user" value="บันทึกการเปลี่ยนแปลง" style="width: 100%; padding: 10px; background-color: #d79577; color: white; border: none; border-radius: 10px; cursor: pointer;">
      <a href="index.php" style="display: block; text-align: center; margin-top: 15px; color:rgb(156, 156, 156);">ยกเลิก</a>
    </form>
  </div>

  <script>
    let provinceData = [];
    const selectedProvince = "<?= $user['province'] ?>";
    const selectedDistrict = "<?= $user['district'] ?>";
    const selectedSubDistrict = "<?= $user['sub_district'] ?>";
    const selectedPostcode = "<?= $user['post_code'] ?>";

    fetch("https://raw.githubusercontent.com/kongvut/thai-province-data/master/api_province_with_amphure_tambon.json")
      .then(res => res.json())
      .then(data => {
        provinceData = data;
        populateProvinces();
      });

    function populateProvinces() {
      const provinceSelect = document.getElementById('province-select');
      provinceData.forEach(p => {
        const opt = new Option(p.name_th, p.name_th, false, p.name_th === selectedProvince);
        provinceSelect.add(opt);
      });
      provinceSelect.dispatchEvent(new Event('change'));
    }

    document.getElementById('province-select').addEventListener('change', function() {
      const province = provinceData.find(p => p.name_th === this.value);
      const districtSelect = document.getElementById('district-select');
      clearOptions(districtSelect);
      clearOptions(document.getElementById('sub-district-select'));
      clearOptions(document.getElementById('postcode-select'));

      province?.amphure.forEach(a => {
        const opt = new Option(a.name_th, a.name_th, false, a.name_th === selectedDistrict);
        districtSelect.add(opt);
      });

      districtSelect.dispatchEvent(new Event('change'));
    });

    document.getElementById('district-select').addEventListener('change', function() {
      const provinceName = document.getElementById('province-select').value;
      const province = provinceData.find(p => p.name_th === provinceName);
      const amphure = province?.amphure.find(a => a.name_th === this.value);
      const subDistrictSelect = document.getElementById('sub-district-select');
      clearOptions(subDistrictSelect);
      clearOptions(document.getElementById('postcode-select'));

      amphure?.tambon.forEach(t => {
        const opt = new Option(t.name_th, t.name_th, false, t.name_th === selectedSubDistrict);
        subDistrictSelect.add(opt);
      });

      subDistrictSelect.dispatchEvent(new Event('change'));
    });

    document.getElementById('sub-district-select').addEventListener('change', function() {
      const provinceName = document.getElementById('province-select').value;
      const districtName = document.getElementById('district-select').value;
      const province = provinceData.find(p => p.name_th === provinceName);
      const amphure = province?.amphure.find(a => a.name_th === districtName);
      const tambon = amphure?.tambon.find(t => t.name_th === this.value);
      const postcodeSelect = document.getElementById('postcode-select');
      clearOptions(postcodeSelect);

      if (tambon) {
        const opt = new Option(tambon.zip_code, tambon.zip_code, false, tambon.zip_code == selectedPostcode);
        postcodeSelect.add(opt);
      }
    });

    function clearOptions(selectElement) {
      selectElement.innerHTML = '<option value="">-- กรุณาเลือก --</option>';
    }
  </script>
</body>

</html>