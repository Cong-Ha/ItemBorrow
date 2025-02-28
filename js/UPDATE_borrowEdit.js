$(document).ready(function () {
    window.borrowEdit = function (borrowId) {
        $('#borrowEdit').html("<div class='text-center'><div class='spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div></div>");

        $.ajax({
            url: 'GET_BorrowEdit.php',
            type: 'GET',
            data: { id: borrowId },
            success: function (response) {
                $('#borrowEdit').html(response);            
            },
            error: function (xhr, status, error) {
                $('#borrowEdit').html("<p class='text-danger'>Error fetching details...</p>");
                console.log("AJAX Error:", error);
            }
        });

        $('#updateModal').modal('show');
    };

    $(document).on('submit', '#updateBorrowForm', function (event) {
        event.preventDefault();

        let formData = new FormData(this);
        formData.append("borrow_id", $('#borrowId').val());

        console.log("form data:", Object.fromEntries(formData));

        $.ajax({
            url: 'UPDATE_BorrowEdit.php',
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false, 
            contentType: false,
            success: function (response) {
                console.log("Server response:", response);

                if (response.status === "error" && response.errors) {
                    // Clear previous error messages
                    $('.text-danger').text("");

                    // Loop through errors and display them
                    Object.keys(response.errors).forEach(function (key) {
                        let errorElement = $("#" + key).siblings(".text-danger"); //target the error span next to the input field
                        if (errorElement.length) {
                            errorElement.text(response.errors[key]);
                        } else {
                            console.warn("Could not find error element for", key);
                        }
                    });
                } else if(response.status === "success"){
                    Swal.fire({
                        title: "Success!",
                        text: "Borrow details updated successfully!",
                        icon: "success",
                        background: "#343a40",
                        color: "#ffffff",
                        confirmButtonColor: "#28a745"
                    }).then(() => {
                        $('#updateModal').modal('hide');
                        location.reload(); // Refresh page after update
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to update borrow details.\n" + xhr.responseText,
                    icon: "error",
                    background: "#343a40",
                    color: "#ffffff",
                    confirmButtonColor: "#dc3545"
                });
            }
        });
    });

    // Clear error messages when the user starts typing
    $(document).on('input change', '#updateBorrowForm input, #updateBorrowForm select', function () {
        $(this).next(".text-danger").text("");  // Clear error message when typing
    });
});
