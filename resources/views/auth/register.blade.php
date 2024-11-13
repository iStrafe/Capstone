<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #121212;
        margin: 0;
    }

    .form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background-color: #ffffff;
        padding: 30px;
        width: 450px;
        border-radius: 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    ::placeholder {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .form button {
        align-self: flex-end;
    }

    .flex-column > label {
        color: #151717;
        font-weight: 600;
    }

    .inputForm {
        border: 1.5px solid #ecedec;
        border-radius: 10px;
        height: 50px;
        display: flex;
        align-items: center;
        padding-left: 10px;
        transition: 0.2s ease-in-out;
    }

    .input {
        margin-left: 10px;
        border-radius: 10px;
        border: none;
        width: 85%;
        height: 100%;
    }

    .input:focus {
        outline: none;
    }

    .inputForm:focus-within {
        border: 1.5px solid #2d79f3;
    }

    .flex-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        justify-content: space-between;
    }

    .flex-row > div > label {
        font-size: 14px;
        color: black;
        font-weight: 400;
    }

    .span {
        font-size: 14px;
        margin-left: 5px;
        color: #2d79f3;
        font-weight: 500;
        cursor: pointer;
    }

    .button-submit {
        margin: 20px 0 10px 0;
        background-color: #151717;
        border: none;
        color: white;
        font-size: 15px;
        font-weight: 500;
        border-radius: 10px;
        height: 50px;
        width: 100%;
        cursor: pointer;
    }

    .button-submit:hover {
        background-color: #252727;
    }

    .p {
        text-align: center;
        color: black;
        font-size: 14px;
        margin: 5px 0;
    }

    .btn {
        margin-top: 10px;
        width: 100%;
        height: 50px;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 500;
        gap: 10px;
        border: 1px solid #ededef;
        background-color: white;
        cursor: pointer;
        transition: 0.2s ease-in-out;
    }

    .btn:hover {
        border: 1px solid #2d79f3;
    }
</style>

<div class="mb-4">
    <form class="form" method="POST" action="{{ route('register') }}">
        @csrf
        <!--Name-->
        <div class="flex-column">
            <label>Name </label>
        </div>
        <div class="inputForm">
            <input id="name" class="input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <!--Email-->
        <div class="flex-column">
            <label>Email </label>
            <x-input-error :messages="$errors->get('email')" class="" />
        </div>
        <div class="inputForm">
            <input id="email" class="input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username">
        </div>

        <!--Password-->
        <div class="flex-column">
            <label>Password </label>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="inputForm">
            <input id="password" class="input"
                            type="password"
                            name="password"
                            required autocomplete="new-password">
        </div>

        <!--Confirm Password-->
        <div class="flex-column">
            <label>Confirm Password </label>
        </div>
        <div class="inputForm">
            <input id="password_confirmation" class="input"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <!--Remember me-->
        <div class="flex-row">
            <!-- Your remember me code here -->
        </div>
        <!--Submit button-->
        <button class="button-submit">Register</button>

        @if (Route::has('register'))
            <p class="p">Already Registered? <a class="span" href="{{ route('login') }}">Login</a>
        @endif
    

    </p><p class="p line">Or With</p>

    <div class="flex-row">
             <a class="btn google" href="{{ route('google-auth')}}">
                <svg xml:space="preserve" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" width="20" version="1.1">
                    <path d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256
                        c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456
                        C103.821,274.792,107.225,292.797,113.47,309.408z" style="fill:#FBBB00;"></path>
                    <path d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451
                        c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535
                        c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z" style="fill:#518EF8;"></path>
                    <path d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512
                        c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771
                        c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z" style="fill:#28B446;"></path>
                    <path d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012
                        c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0
                        C318.115,0,375.068,22.126,419.404,58.936z" style="fill:#F14336;"></path>

                </svg>
                Google 
             </a>
        </div>

    </form>
</div>