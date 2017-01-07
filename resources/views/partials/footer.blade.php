<!-- Footer -->

        <footer id="footer" class="wrapper style1-alt">
            <div class="inner">
                <ul class="menu">
                    <li>&copy; Evolink Technologies. All rights reserved.</li>
                    <li>Design & Developed: <a href="http://techinn.biz">TechInn Consultants</a></li>
                </ul>
            </div>
        </footer>

<!-- Scripts -->
        <script src="{{ url('prinquiry/assets/js') }}/jquery.min.js"></script>
        <script src="{{ url('prinquiry/assets/js') }}/jquery.scrollex.min.js"></script>
        <script src="{{ url('prinquiry/assets/js') }}/jquery.scrolly.min.js"></script>
        <script src="{{ url('prinquiry/assets/js') }}/skel.min.js"></script>
        <script src="{{ url('prinquiry/assets/js') }}/util.js"></script>
        <script src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
        <!--[if lte IE 8]><script src="{{ url('prinquiry/assets/js') }}/ie/respond.min.js"></script><![endif]-->
        <script src="{{ url('prinquiry/assets/js') }}/main.js"></script>
        <script src="{{ url('prinquiry/bootstrap/js') }}/bootstrap.js"></script>
        <script src="{{ url('prinquiry/assets/js/') }}/jquery.steps.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
        
        <script>
        
            $('.datepicker').datepicker({
                autoclose: true,
                dateFormat: "yy-m-d"
            });
            $(document).ready(function() {
                
            });
           $('#reset-inquiry-details').click(function(){
              $('#supplier_id input[type="text"]').val('');
           });
           
            $( "#accordion" ).accordion();
            $("#frmInquiry").validate({
                rules: {
                    "inqpost[1][partnum]": {
                        required: true
                    }
                }
            });
//            _.templateSettings.variable = "element";
            var tpl = $("#frmInquiry").html();

            var counter = 1;
            var form  = $('#frmInquiry');
            $("#inquiry-wizard").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                autoFocus: true,
                onStepChanging: function (event, currentIndex, newIndex)
                {
                    if(currentIndex == 1){
                    }
                    // Used to skip the "Warning" step if the user is old enough.
                    var fields = $( "#rows1  :input" ).serializeArray();
                    if (fields[0].value != '')
                    {
                        return true;
                    }
                    // Forbid next action on "Warning" step if the user is to young
                    if (fields[0].value === '')
                    {
                        alert('Please enter atleast one inquiry');
                        return false;
                    }
                },
                onStepChanged: function (event, currentIndex, priorIndex)
                {
                    if (currentIndex === 2 && Number($("#age-2").val()) >= 18)
                    {
                        form.steps("next");
                    }
                    // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
                    if (currentIndex === 2 && priorIndex === 3)
                    {
                        form.steps("previous");
                    }
                },
                onFinishing: function (event, currentIndex)
                {
                    form.submit();
                },
                onFinished: function (event, currentIndex)
                {
                    alert("Submitted!");
                }
            });
            $("#btnadd").click(function(){
                var i = $("#inquiry_details div.inquiry-fields").length+1;
                var j = $("#inquiry_details div.inquiry-fields").length+11;
                if($("#inquiry_details div.inquiry-fields").length < 100){
                    for(;i < j ; i++){
                        var intId = i;
                        var fieldWrapper = $("<div class=\"row uniform inquiry-fields\" id=\"rows" + intId + "\"/>");
                        var removeButton = $("<div class='2u 12u$(xsmall)'><input type=\"button\" class=\"remove\" value=\"Remove\" /></div>");
                            removeButton.click(function() {
                                $(this).parent().remove();
                        });
                        fieldWrapper.append($('#rows1').html());
                        $("#inquiry_details").append(fieldWrapper);
                    }
                    $('#btnremove-li').css('display','block');
                }
            });
            
            $('#btnremove').click(function(){
                var i = $("#inquiry_details div.inquiry-fields").length;
                if($("#inquiry_details div.inquiry-fields").length < 11){
                    var j = 1;
                }else{
                     var j = $("#inquiry_details div.inquiry-fields").length-10;
                }
                for(;i > j; i--){
                    $('#rows'+i).remove();
                }
                if($("#inquiry_details div.inquiry-fields").length < 11){
                    $('#btnremove-li').css('display','none');
                }
            });
            $("#checkbox_preffered_supplier").change(function () {
                $("input#preffered_supplier").prop('checked', $(this).prop("checked"));
            });
            $("#checkbox_supplier").change(function () {
                $("input#supplier").prop('checked', $(this).prop("checked"));
                if($('#remaining_users').is(":visible")) {
                    $("#remaining_users input#supplier").prop('checked', $(this).prop("checked"));
                }else {
                    $("#remaining_users input#supplier").prop('checked', false);
                }
            });
            $('#moreSupplier').click(function(){
                $('#remaining_users').fadeToggle();
            })
             $('#inquiry-table').dataTable( {
                "columnDefs": [
                    { "type": "numeric-comma", targets: 3 }
                ]
            });
            $("#search").on("keyup", function() {
                var value = $(this).val();

                $("table#supplier-list tr").each(function(index) {
                    if (index !== 0) {

                        $row = $(this);

                        var id = $row.find("td:first label span").text().toLowerCase();
                        if (id.indexOf(value.toLowerCase()) !== 0) {
                            $row.hide();
                        }
                        else {
                            $row.show();
                        }
                    }
                });
                $("table#supplier-list-more tr").each(function(index) {
                    if (index !== 0) {

                        $row = $(this);

                        var id = $row.find("td:first label span").text().toLowerCase();
                        if (id.indexOf(value.toLowerCase()) !== 0) {
                            $row.hide();
                        }
                        else {
                            $row.show();
                        }
                    }
                });
            });
        </script>
</body>
</html>