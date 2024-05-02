<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <style>

        

    </style>
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
        </div>

        <div class="about-section">
        <img src="images\cute-feline-cate.jpg" alt="Jane" style="width:100%; height: 300px;">
        </div>

       
       
       
        <div class="container">
        <h1>Report Cats</h1>
        <div class="row">

            <div class="g-col-6">
                
            <div class="card" style="width: 18rem;">
            <div class="card-body">
  
            <div class="form-check">
           <input class="form-check-input position-static" type="checkbox" id="blankCheckbox" value="option1" aria-label="...">Lost
           </div>

           <div class="form-check">
           <input class="form-check-input position-static" type="checkbox" id="blankCheckbox" value="option1" aria-label="...">Found/Stray
           </div>


               <form>
               <div class="form-group">
              <label for="formGroupExampleInput2">Date</label>
              <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
               </div>

                   <div class="form-group">
                  <label for="formGroupExampleInput2">Pet Name</label>
                  <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                 </div>

                     <div class="form-group">
                     <label for="formGroupExampleInput2">Sex</label>
                     <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                    </div>
                           <div class="form-group">
                           <label for="formGroupExampleInput2">Color</label>
                            <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                            </div>

                                   <div class="form-group">
                                 <label for="formGroupExampleInput2">Size</label>
                                 <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                                </div>

                                     <div class="form-group">
                                       <label for="formGroupExampleInput2">Contact Email</label>
                                      <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                                    </div>

                                          <div class="form-group">
                                         <label for="formGroupExampleInput2">Nearest Location Seen</label>
                                         <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                                         </div>
                                              
                                             <div class="form-group">
                                               <label for="formGroupExampleInput2">Upload Picture</label>
                                            <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="">
                                             </div>
  </div>
  </div>
</form>



  </div>
</div>

            </div>






        </div>
      
</body>
