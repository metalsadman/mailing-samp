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
        <div class="card shadow-sm" style="width: 40rem;">
            <div class="card-body">
            <form class="m-1" id="address-form">
                <div class="form-group">
                    <label for="address1">Address Line 1</label>
                    <input type="text" class="form-control" id="address1" name="address1">
                </div>
                <div class="form-group">
                    <label for="address2">Address Line 2</label>
                    <input type="text" class="form-control" id="address2" name="address2">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="city" name="city">
                </div>
                <div class="form-group">
                    <label for="state">State</label>
                    <select class="form-control" id="state" name="state">
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
                    <input type="text" class="form-control" id="zip" name="zip">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="true" id="standardized" name="standardized">
                    <label class="form-check-label" for="standardized">
                        Standardized Address
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                <div class="alert alert-danger my-3" role="alert" id="error" style="display:none;"></div>
                <div class="alert alert-success my-3" role="alert" id="success" style="display:none;"></div>
            </form>
            </div>            
        </div>
    </div>
    <script>
        // Get the form element
        const form = document.getElementById("address-form");
        const errorAlertEl = document.getElementById("error");
        const successAlertEl = document.getElementById("success");
        const submitBtn = document.getElementById('submitBtn');

        // Listen for the form submit event
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "submit.php", true);
            const formData = new FormData(form);
            xhr.send(formData);
            submitBtn.disabled = true;

            xhr.onload = () => {
                submitBtn.disabled = false;
                const { message } = JSON.parse(xhr.responseText)
                if (xhr.readyState === 4 && xhr.status === 200) {
                    showHideAlert(successAlertEl, errorAlertEl, message)
                } else {
                    showHideAlert(errorAlertEl, successAlertEl, message)
                }
            };
        });

        function showHideAlert(showEl, hideEl, msg){
            showEl.style.display = 'block';
            showEl.innerHTML = msg;
            hideEl.style.display = 'none';
        }
    </script>
</body>
</html>