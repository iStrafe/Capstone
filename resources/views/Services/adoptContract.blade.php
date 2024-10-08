

<style>
    .plan {
        border-radius: 16px;
        box-shadow: 0 30px 30px -25px rgba(0, 38, 255, 0.205);
        padding: 10px;
        background-color: #fff;
        color: #697e91;
        max-width: 760px;
    }

    .plan strong {
        font-weight: 600;
        color: #425275;
    }

    .plan .inner {
        align-items: center;
        padding: 20px;
        padding-top: 40px;
        background-color: #ecf0ff;
        border-radius: 12px;
        position: relative;
    }

    .plan .pricing {
        position: absolute;
        top: 0;
        right: 0;
        background-color: #bed6fb;
        border-radius: 99em 0 0 99em;
        display: flex;
        align-items: center;
        padding: 0.625em 0.75em;
        font-size: 1.25rem;
        font-weight: 600;
        color: #425475;
    }

    .plan .pricing small {
        color: #707a91;
        font-size: 0.75em;
        margin-left: 0.25em;
    }

    .plan .title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #425675;
    }

    .plan .title + * {
        margin-top: 0.75rem;
    }

    .plan .info + * {
        margin-top: 1rem;
    }

    .plan .features {
        display: flex;
        flex-direction: column;
    }

    .plan .features li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .plan .features li + * {
        margin-top: 0.75rem;
    }

    .plan .features .icon {
        background-color: #1FCAC5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
    }

    .plan .features .icon svg {
        width: 14px;
        height: 14px;
    }

    .plan .features + * {
        margin-top: 1.25rem;
    }

    .plan .action {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: end;
    }

    .plan .button {
        background-color: #6558d3;
        border-radius: 6px;
        color: #fff;
        font-weight: 500;
        font-size: 1.125rem;
        text-align: center;
        border: 0;
        outline: 0;
        width: 50%;
        padding: 0.625em 0.75em;
        text-decoration: none;
    }

    .plan .button:hover, .plan .button:focus {
        background-color: #4133B7;
    }

    .plan .button.enabled {
        cursor: pointer;
        opacity: 1;
    }
</style>

    <!-- Modal -->
    <div class="modal fade" id="modalId2" tabindex="-1"role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                     <div class="text-center" style="font-family: 'HK Modular', sans-serif; font-size: 1.5rem; font-weight: bold;">
                        <p class="title" style="color: white;">AduCats ADOPTION CONTRACT</p>    </div>
                        <div class="plan">
                            <div class="inner">
                                <p class="title">The prospective caregiver agrees:</p>
                    <ul class="features">
                        @for ($i = 1; $i <= 12; $i++)
                            <li>
                                <span class="icon">
                                    <svg height="20" width="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h24v24H0z" fill="none"></path>
                                        <path fill="currentColor" d="M10 15.172l9.192-9.193 1.415 1.414L10 18l-6.364-6.364 1.414-1.414z"></path>
                                    </svg>
                                </span>
                                <span>
                                    @switch($i)
                                        @case(1)
                                            To provide a Vallid ID.
                                            @break
                                        @case(2)
                                            That the cat is to live in a private residence as a companion animal.
                                            @break
                                        @case(3)
                                            To provide the cat with sufficient quantitties of nuttricious food and refresh water each day.
                                            @break
                                        @case(4)
                                            Never to strike or other wise harm the cat.
                                            @break
                                        @case(5)
                                            Never to have the cat declawed.
                                            @break
                                        @case(6)
                                            To spray/neuter the cat by 5 months of age. Proof of surgery must be mailed to the
                                            original caregiver within 30 days of the procedure.
                                            @break
                                        @case(7)
                                            To ensure that the cat's vaccination (rabies and 4-in-1) are current and to provide
                                            veterinary care upon sickness,disease, or injury.
                                            @break
                                        @case(8)
                                            To provide the original caregiver with the contact information of the veterinarian and to 
                                            give tthe original caregiver premission to talk to access cat's medical records via the aforementioned veterinarian.
                                            @break
                                        @case(9)
                                            To give the original caregiver regular updates regarding the cat;s health status through pictures wich may be done
                                            via Facebook.
                                            @break
                                        @case(10)
                                            To give the original caregiver the rights to verify that the terms of this adoption agreement are being observed.
                                            @break
                                        @case(11)
                                            That if the car must be relinquished for any reason by the adopter, s/he MUST NOT
                                            turn the ca over too a humane society, sheler or person, but must return the cat to the original caregiver
                                            OR AdUCats representative.
                                            @break
                                        @case(12)
                                            That failure to perform the foregoing agreement will cnstitutue a breach of contract. In
                                            the event of any such breach of contract, the original caregiver has he right or reclaim possession of the adoptetd cat.
                                            @break
                                        @default
                                            team members
                                    @endswitch
                                </span>
                            </li>
                        @endfor
                    </ul>
                <div class="action">
                    <div class="px-5">
                        <input type="radio" id="agree" name="agreement" value="agree">
                        <label for="agree">I agree to the terms</label>
                    </div>
                    <a class="button" id="adoptButton" href="{{url('#')}}" onclick="return false;">
                        Adopt a Cat
                    </a>
                </div>
            </div>
        </div>
                </div>
               
            </div>
        </div>
    </div>
    
    <script>/**Script for Checkbox and Button */
        var modalId = document.getElementById('modalId2');
        var agreeCheckbox = document.getElementById('agree');
        var adoptButton = document.getElementById('adoptButton');

        agreeCheckbox.addEventListener('change', function () {
            if (agreeCheckbox.checked) {
                adoptButton.classList.add('enabled');
                adoptButton.removeAttribute('disabled');
                adoptButton.setAttribute('href', 'cats');
            } else {
                adoptButton.classList.remove('enabled');
                adoptButton.setAttribute('disabled', 'disabled');
                adoptButton.setAttribute('href', '#');
            }
        });

        adoptButton.addEventListener('click', function (event) {
            if (!agreeCheckbox.checked) {
                event.preventDefault();
                alert("Please read the terms and conditions before proceeding to the adoption page.");
            }else{
                window.location.href = 'cats';    }
        });

        modalId.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            let button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            let recipient = button.getAttribute('data-bs-whatever');
        });
    </script>
    

       
 