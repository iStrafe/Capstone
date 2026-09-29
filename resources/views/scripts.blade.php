<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
         <!-- Scripts -->
        @vite('resources/sass/app.scss')
        @vite('resources/js/app.js')
        
        <!--Dropdown-->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

         <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />

        <!-- Styles -->
        <style>
            /* Base reset the site's pages are laid out against. It replaces a Tailwind
               preflight and utility block that was pasted here from the Laravel welcome page.
               Bootstrap buttons (.btn, and the icon-only .btn-close) are left out of the button
               reset so they keep their own colours; the old rule made "Update Cat", "Update"
               and "Add Another ID" white text on a transparent background. */
            *, ::after, ::before { box-sizing: border-box; border-width: 0; border-style: solid; border-color: #e5e7eb; }
            html { line-height: 1.5; -webkit-text-size-adjust: 100%; tab-size: 4; -webkit-tap-highlight-color: transparent; }
            body { margin: 0; line-height: inherit; }
            hr { height: 0; color: inherit; border-top-width: 1px; }
            h1, h2, h3, h4, h5, h6 { font-size: inherit; font-weight: inherit; }
            a { color: inherit; text-decoration: inherit; }
            b, strong { font-weight: bolder; }
            small { font-size: 80%; }
            table { text-indent: 0; border-color: inherit; border-collapse: collapse; }
            button, input, optgroup, select, textarea { font-family: inherit; font-size: 100%; font-weight: inherit; line-height: inherit; color: inherit; margin: 0; padding: 0; }
            button, select { text-transform: none; }
            button:where(:not(.btn, .btn-close:empty)) { background-color: transparent; background-image: none; }
            :is([type=button], [type=reset], [type=submit]):where(:not(.btn, .btn-close:empty)) { background-color: transparent; background-image: none; }
            blockquote, dd, dl, figure, h1, h2, h3, h4, h5, h6, hr, p, pre { margin: 0; }
            fieldset { margin: 0; padding: 0; }
            legend { padding: 0; }
            menu, ol, ul { list-style: none; margin: 0; padding: 0; }
            textarea { resize: vertical; }
            input::placeholder, textarea::placeholder { opacity: 1; color: #9ca3af; }
            [role=button], button { cursor: pointer; }
            :disabled { cursor: default; }
            audio, canvas, embed, iframe, img, object, svg, video { display: block; vertical-align: middle; }
            img, video { max-width: 100%; height: auto; }
            [hidden] { display: none; }

            table, th, td {
                border: 1px solid black;
                text-align: center;
                }

             body{
                background-color: #ff9966;
             }

             *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }

            .container h1{
                font-size: 40px;
                text-align: center;
                padding-top: 5%;
                font-weight: 600;
                position: relative;
            }




            /*=======Cat gallery grid (home and user dashboard) ==========*/
            /* Scoped to .cat-gallery so Bootstrap's .row/.col-* keep working everywhere else. */
            .cat-gallery {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr));
                gap: 30px;
            }

            .cat-gallery .card {
                width: 100%;
                max-width: 25em;
                margin: 0 auto;
            }

           

                                /* === removing default button style ===*/
                    .button {
                    margin: 0;
                    height: auto;
                    background: transparent;
                    padding: 0;
                    border: none;
                    cursor: pointer;
                    }

                    /* button styling */
                    .button {
                    --border-right: 6px;
                    --text-stroke-color: rgba(255,255,255,0.6);
                    --animation-color: #90E0EF;
                    --fs-size: 2em;
                    letter-spacing: 3px;
                    text-decoration: none;
                    font-size: var(--fs-size);
                    font-family: "Arial";
                    position: relative;
                    text-transform: uppercase;
                    color: transparent;
                    -webkit-text-stroke: 1px var(--text-stroke-color);
                    }
                    /* this is the text, when you hover on button */
                    .hover-text {
                    position: absolute;
                    box-sizing: border-box;
                    content: attr(data-text);
                    color: var(--animation-color);
                    width: 0%;
                    inset: 0;
                    border-right: var(--border-right) solid var(--animation-color);
                    overflow: hidden;
                    transition: 0.5s;
                    -webkit-text-stroke: 1px var(--animation-color);
                    }
                    /* hover */
                    .button:hover .hover-text {
                    width: 100%;
                    filter: drop-shadow(0 0 23px var(--animation-color))
                    }
                 /* === removing default button style ===*/

                 /* From Uiverse.io by adamgiebl */ 
                 
        </style>


        <style>
               header.masthead {
                padding-top: 10.5rem;
                padding-bottom: 6rem;
                text-align: center;
                color: #fff;
                background-image: url("images/Cat2.jpg");
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-position: center center;
                background-size: cover;
                }
                header.masthead .masthead-subheading {
                font-size: 1.5rem;
                font-style: italic;
                line-height: 1.5rem;
                margin-bottom: 25px;
                font-family: "Roboto Slab", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                }
                header.masthead .masthead-heading {
                font-size: 3.25rem;
                font-weight: 700;
                line-height: 3.25rem;
                margin-bottom: 2rem;
                font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                }

                @media (min-width: 768px) {
                header.masthead {
                    padding-top: 17rem;
                    padding-bottom: 12.5rem;
                }
                header.masthead .masthead-subheading {
                    font-size: 2.25rem;
                    font-style: italic;
                    line-height: 2.25rem;
                    margin-bottom: 2rem;
                }
                header.masthead .masthead-heading {
                    font-size: 4.5rem;
                    font-weight: 700;
                    line-height: 4.5rem;
                    margin-bottom: 4rem;
                }
                }
        </style>
        

    </head>