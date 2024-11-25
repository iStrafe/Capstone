<style>
    /* From Uiverse.io by micaelgomestavares */ 
    .form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 700px;
        background-color: #fff;
        padding: 20px;
        border-radius: 20px;
        position: relative;
    }

    .title {
        font-size: 28px;
        color: royalblue;
        font-weight: 600;
        letter-spacing: -1px;
        position: relative;
        display: flex;
        align-items: center;
        padding-left: 30px;
    }

    .title::before, .title::after {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        border-radius: 50%;
        left: 0px;
        background-color: royalblue;
    }

    .title::before {
        width: 18px;
        height: 18px;
        background-color: royalblue;
    }

    .title::after {
        width: 18px;
        height: 18px;
        animation: pulse 1s linear infinite;
    }

    .message, .signin {
        color: rgba(88, 87, 87, 0.822);
        font-size: 14px;
    }

    .signin {
        text-align: center;
    }

    .signin a {
        color: royalblue;
    }

    .signin a:hover {
        text-decoration: underline royalblue;
    }

    .flex {
        display: flex;
        width: 100%;
        gap: 6px;
    }

    .form label {
        position: relative;
    }

    .form label .input {
        width: 100%;
        padding: 10px 100px 20px 10px;
        outline: 0;
        border: 1px solid rgba(105, 105, 105, 0.397);
        border-radius: 20px;
    }

    .form label .input + span {
        position: absolute;
        left: 10px;
        top: 15px;
        color: grey;
        font-size: 0.9em;
        cursor: text;
        transition: 0.3s ease;
    }

    .form label .input:placeholder-shown + span {
        top: 15px;
        font-size: 0.9em;
    }

    .form label .input:focus + span, .form label .input:valid + span {
        top: 0px;
        font-size: 0.7em;
        font-weight: 600;
    }

    .form label .input:valid + span {
        color: green;
    }

    .submit {
        border: none;
        outline: none;
        background-color: royalblue;
        padding: 10px;
        border-radius: 10px;
        color: #fff;
        font-size: 16px;
        transform: .3s ease;
    }

    .submit:hover {
        background-color: rgb(56, 90, 194);
        cursor: pointer;
    }

    @keyframes pulse {
        from {
            transform: scale(0.9);
            opacity: 1;
        }

        to {
            transform: scale(1.8);
            opacity: 0;
        }
    }
</style>
<!-- Modal -->
<div class="modal fade" id="adoptionFormModal" tabindex="-1" aria-labelledby="addCatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form class="form" action="{{ route('adoption.request') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <p class="title">ADOPTION REQUEST FORM</p>
                    <p class="message"></p>
                    <div class="">
                        <label>
                            <input class="input" type="text" name="name" placeholder="" required>
                            <span>Name</span>
                        </label>

                        <label>
                            <input class="input" type="text" name="address" placeholder="" required>
                            <span>Address</span>
                        </label>
                    </div>  

                    <div class="">
                        <label>
                            <input class="input" type="email" name="email" placeholder="" required>
                            <span>Email</span>
                        </label>
                    </div>
                    
                    <label>
                        <input class="input" type="text" name="phone" placeholder="" required>
                        <span>Phone Number (Optional)</span>
                    </label>


                    <div id="valid-id-container">
                        <div class="valid-id-field">
                            <label for="valid_id">Valid ID (Upto 2 only):</label>
                            <input type="file" name="valid_id[]" class="form-control">
                        </div>
                    </div>
                    <button type="button" id="add-valid-id" class="btn btn-secondary">Add Another ID</button>

                    <div class="py-3">
                        <label>
                            <input class="input py-3" type="text" name="name_of_cat" placeholder="" required>
                            <span>Cat Name</span>
                        </label>

                        <label>
                            <input class="input py-3" type="text" name="approximate_age" placeholder="" required>
                            <span>Age</span>
                        </label>

                        <label>
                            <input class="input py-3" type="text" name="sex" placeholder="" required>
                            <span>Sex</span>
                        </label>

                        <label>
                            <input class="input py-3" type="text" name="color" placeholder="" required>
                            <span>Color</span>
                        </label>

                        <label>
                            <input class="input py-3" type="text" name="breed" placeholder="" required>
                            <span>Breed</span>
                        </label>

                        <label>
                            <label for="date_of_adoption">Date of Adoption</label>
                            <input class="input py-3" type="date" name="date_of_adoption" placeholder="" required>
                        </label>
                    
                    </div>

                    <button href="{{ url('/') }}" class="submit" value="Save new info">Submit</button>
                  
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-valid-id').addEventListener('click', function() {
        var container = document.getElementById('valid-id-container');
        var validIdFields = container.getElementsByClassName('valid-id-field');
        if (validIdFields.length < 2) {
            var newField = document.createElement('div');
            newField.classList.add('valid-id-field');
            newField.innerHTML = `
                <label for="valid_id">Valid ID:</label>
                <input type="file" name="valid_id[]" class="form-control">
            `;
            container.appendChild(newField);
        } else {
            alert('You can only add up to 2 valid IDs.');
        }
    });

    document.getElementById('valid-id-container').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-valid-id')) {
            event.target.parentElement.remove();
        }
    });
</script>