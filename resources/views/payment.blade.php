<style>
      /* Paymongo moadal*/ 
            .orange {
            color: #ff7a01;
            }

            .form-container {
            max-width: 700px;
            margin: 30px;
            background-color: #001925;
            padding: 30px;
            border-left: 5px solid #00B4D8;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%);
            }

            .heading {
            display: block;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            }

            .modalText{
            display: block;
            color: #06d6a0;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 0.5;
            font-weight: 400;
            margin-bottom: 20px;
            }

            .form-container .form .input {
            color: #87a4b6;
            width: 100%;
            background-color: #002733;
            border: none;
            outline: none;
            padding: 10px;
            margin-bottom: 20px;
            font-weight: bold;
            transition: all 0.2s ease-in-out;
            border-left: 1px solid transparent;
            }

            .form-container .form .input:focus {
            border-left: 5px solid #00B4D8;
            }

            .form-container .form .textarea {
            width: 100%;
            padding: 10px;
            border: none;
            outline: none;
            background-color: #013747;
            color: #ff7a01;
            font-weight: bold;
            resize: none;
            max-height: 150px;
            margin-bottom: 20px;
            border-left: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            }

            .form-container .form .textarea:focus {
            border-left: 5px solid #ff7a01;
            }

            .form-container .form .button-container {
            display: flex;
            gap: 10px;
            }

            .form-container .form .button-container .send-button {
            flex-basis: 70%;
            background: #06d6a0;
            padding: 10px;
            color: #edf2fb;
            text-align: center;
            font-weight: bold;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            }

            .form-container .form .button-container .send-button:hover {
            background: transparent;
            border: 1px solid #76c893;
            color: #CAF0F8;
            }

            .form-container .form .button-container .reset-button-container {
            filter: drop-shadow(1px 1px 0px #f72585);
            flex-basis: 30%;
            }

            .form-container .form .button-container .reset-button-container .reset-button {
            position: relative;
            text-align: center;
            padding: 10px;
            color: #f72585;
            font-weight: bold;
            background: #001925;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%);
            transition: all 0.2s ease-in-out;
            }

            .form-container .form .button-container .reset-button-container .reset-button:hover {
            background: #f72585;
            color: #001925;
            }
</style>

                <!-- Modal Body -->
                <!-- Paymongo Modal -->
                <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
                        <div class="modal-content form-container">
                        <div class="modal-header form">
                            <span class="heading">Donate via PayMongo</span>
                            <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close">CLOSE</button>
                        </div>
                    <div class="form">
                        <form action="{{ route('paymongo.create') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="modalText" for="amount">Enter Amount (PHP):</label>
                                <input class="input" placeholder="Amount" type="number" name="amount" id="amount" required>
                            </div>
                            <div class="mb-3">
                                <label class="modalText" for="amount">Description</label>
                                <textarea class="input" type="text" name="description" id="description" required></textarea>
                            </div>
                                <div class="button-container">
                                    <button type="submit" class="send-button">Send</button>
                                    <div class="reset-button-container">
                                        <button type="reset" id="reset-btn" class="reset-button">Reset</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
                </div>
                </div>

        <!-- Bootstrap Modal Initialization Script -->
        <script>
        const myModal = new bootstrap.Modal(document.getElementById('modalId'));
        </script>
