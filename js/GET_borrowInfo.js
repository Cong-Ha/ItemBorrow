$(document).ready(function(){
    window.borrowInfo = function(borrowId){
        $('#borrowDetails').html
            ("<div id='spinner' class='spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div>");

        $.ajax({
            url: 'GET_BorrowInfo.php',
            type: 'GET',
            data: {id: borrowId},
            success: function(response) {
                //console.log("Response recieved", response);
                $('#borrowDetails').html(response);
            },
            error: function(xhr, status, error){
                //console.log("AJAX Error:", error);
                $('#borrowDetails').html("<p class='text-danger>error fetching details...</p>", error);
            }
        });

        //show modal if not open
        $('#infoModal').modal('show');
    }
});