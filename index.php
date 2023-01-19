<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    
    <title>Mailing Address</title>
    <style>
        body {background-color: #E7E9EB;}
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <form class="m-1" id="address-form">
            <div class="card shadow-sm col-12">
                <div class="card-body  m-1">
                    <div class="fs-4 fw-semibold">Address Validator</div>
                    <span>Validate/Standardizes addresses using USPS</span>
                    <hr/>
                    <div class="row gy-3">
                        <div class="form-group">
                            <label for="address1">Address Line 1</label>
                            <input type="text" class="form-control mt-2" id="address1" name="address1" value="Suite 6100">
                        </div>
                        <div class="form-group">
                            <label for="address2">Address Line 2</label>
                            <input type="text" class="form-control mt-2" id="address2" name="address2" value="185 Berry St">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control mt-2" id="city" name="city" value="San Francisco">
                        </div>
                        <div class="form-group">
                            <label for="state">State</label>
                            <select class="form-control mt-2" id="state" name="state">
                                <option value=""></option>
                                <?php
                                require('states.php');
                                foreach($states as $key => $value) {
                                    echo "<option value='$key'>$value</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zip">Zip Code</label>
                            <input type="text" class="form-control mt-2" id="zip" name="zip" value="94556">
                        </div>
                        <div class="form-group alert alert-danger my-3" role="alert" id="error" style="display:none;"></div>

                        <div class="form-group text-center">
                            <button type="button" class="btn btn-primary px-3 mt-2" id="submitBtn" onclick="validate()">VALIDATE</button>
                        </div>
                        <button id="showModal" type="button" style="display: none" data-bs-toggle="modal" data-bs-target="#exampleModal"></button>
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-4" id="exampleModalLabel">Save Address</h1>
                                </div>
                                <div class="modal-body">
                                    <div>Which address format do you want to save?</div>
                                    <div class="mb-2">                                        
                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="btnradio1" onclick="changeAddressDisplay(0)">ORIGINAL</label>
                                            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="btnradio2" onclick="changeAddressDisplay(1)">STANDARDIZED (USPS)</label>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="font-monospace text-muted">
                                                Address Line 1: <span id="addDisplay"></span><br>
                                                Address Line 2: <span id="addDisplay"></span><br>
                                                City: <span id="addDisplay"></span><br>
                                                State: <span id="addDisplay"></span><br>
                                                Zip Code: <span id="addDisplay"></span><br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-success my-3" role="alert" id="success" style="display:none;"></div>
                                    <div class="form-group alert alert-danger my-3" role="alert" id="error2" style="display:none;"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="save()" id="saveBtn">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                    
            </div>
        </form>
    </div>
    <script>
        const form = document.getElementById("address-form");
        const errorAlertEl = document.getElementById("error");
        const successAlertEl = document.getElementById("success");
        const submitBtn = document.getElementById('submitBtn');
        const showModalBtn = document.getElementById('showModal');
        const orig = document.getElementById('btnradio1');
        const usps = document.getElementById('btnradio2');
        const saveBtn = document.getElementById('saveBtn');
        let origAddress = {};
        let uspsAddress = {};
        let whichAddress = 0;
        
        function validate(){
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "validate.php", true);
            const formData = new FormData(form);
            xhr.send(formData);
            submitBtn.disabled = true;
            for (const [key, val]  of formData.entries()) {                
                key !=='btnradio' && (origAddress[key] = val)
            }

            xhr.onload = () => {
                submitBtn.disabled = false;
                const { message, data } = JSON.parse(xhr.responseText)
                if (xhr.readyState === 4 && xhr.status === 200) {
                    errorAlertEl.style.display = 'none';
                    showModal.click();
                    uspsAddress = data;
                    changeAddressDisplay(1);
                    saveBtn.disabled = false;
                } else {                    
                    errorAlertEl.style.display = 'block';
                    errorAlertEl.innerHTML = message;
                }
            };
        }

        const addDisplays = document.querySelectorAll('#addDisplay')
        function displayData(data){            
            Object.keys(data).forEach((k,i) => {
                addDisplays[i].textContent = data[k]
            })
        }

        function changeAddressDisplay(btn){
            whichAddress = btn
            displayData(btn === 1 ? uspsAddress : origAddress)
        }

        function showHideAlert(showEl, hideEl, msg){
            showEl.style.display = 'block';
            showEl.innerHTML = msg;
            hideEl.style.display = 'none';
        }

        
        const errorAlertEl2 = document.getElementById("error2");
        function save(){
            const params = whichAddress === 1 ? uspsAddress : origAddress;
            let fd = new FormData()

            Object.keys(params).forEach(k => {
                fd.append(k, params[k])
            })
            
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save.php", true);
            xhr.send(fd);

            saveBtn.disabled = true;
            xhr.onload = () => {
                submitBtn.disabled = false;
                const { message, data } = JSON.parse(xhr.responseText)
                if (xhr.readyState === 4 && xhr.status === 200) {
                    showHideAlert(successAlertEl, errorAlertEl2, message)
                } else {
                    showHideAlert(errorAlertE2, successAlertEl, message)
                }
            };
        }

    </script>
</body>
</html>