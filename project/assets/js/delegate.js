document.addEventListener('DOMContentLoaded', function() {
    const delegateForm = document.getElementById('delegateRegistrationForm');
    if (delegateForm) {
        delegateForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Basic validation
            const terms = document.getElementById('terms_agree');
            const privacy = document.getElementById('privacy_agree');
            
            if (!terms.checked || !privacy.checked) {
                alert("You must agree to the Terms and Conditions and Privacy Policy.");
                return;
            }

            const formData = new FormData(this);
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';

            try {
                const response = await fetch('api/index.php/delegate/register', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                overlay.style.display = 'none';

                if (data.success) {
                    // Redirect to pending approval page
                    window.location.href = `delegate-pending.php?id=${data.delegate_id}`;
                } else {
                    alert(data.message || "An error occurred during registration. Please try again.");
                }
            } catch (error) {
                overlay.style.display = 'none';
                console.error("Error submitting form:", error);
                alert("A network error occurred. Please try again.");
            }
        });
    }

    // Dynamic file size validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert("File size exceeds 5MB limit. Please choose a smaller file.");
                    this.value = ''; // clear
                }
            }
        });
    });

    // Toggle "Other" delegate type field
    const delegateTypeSelect = document.getElementById('delegate_type');
    const delegateTypeOtherGroup = document.getElementById('delegate_type_other_group');
    const delegateTypeOtherInput = document.getElementById('delegate_type_other');
    
    if (delegateTypeSelect && delegateTypeOtherGroup && delegateTypeOtherInput) {
        delegateTypeSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                delegateTypeOtherGroup.style.display = 'block';
                delegateTypeOtherInput.required = true;
            } else {
                delegateTypeOtherGroup.style.display = 'none';
                delegateTypeOtherInput.required = false;
                delegateTypeOtherInput.value = ''; // Clear value when hidden
            }
        });
    }
});
