// ===== Contact Form Validation =====
document.addEventListener('DOMContentLoaded', function () {

  const form = document.getElementById('contactForm');

  if (!form) return;

  const nameInput = document.getElementById('name');
  const emailInput = document.getElementById('email');
  const orderNumberInput = document.getElementById('orderNumber');
  const subjectInput = document.getElementById('subject');
  const messageInput = document.getElementById('message');
  const successMsg = document.getElementById('formSuccess');

  form.addEventListener('submit', function (e) {
    let isValid = true;

    // Reset previous errors
    clearError('nameError');
    clearError('emailError');
    clearError('orderNumberError');
    clearError('subjectError');
    clearError('messageError');
    successMsg.textContent = '';

    // Validate Name
    if (nameInput.value.trim().length < 3) {
      showError('nameError', 'Please enter your full name (at least 3 characters).');
      isValid = false;
    }

    // Validate Email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value.trim())) {
      showError('emailError', 'Please enter a valid email address.');
      isValid = false;
    }

    // Validate Order Number (optional, but if filled must look valid)
    if (orderNumberInput.value.trim() !== '' && orderNumberInput.value.trim().length < 4) {
      showError('orderNumberError', 'Order number seems too short. Please check and try again.');
      isValid = false;
    }

    // Validate Subject
    if (subjectInput.value === '') {
      showError('subjectError', 'Please select a subject.');
      isValid = false;
    }

    // Validate Message
    if (messageInput.value.trim().length < 10) {
      showError('messageError', 'Message should be at least 10 characters long.');
      isValid = false;
    }

    // This is a progressive-enhancement layer only: if any field fails
    // client-side validation we stop the submit and show inline errors.
    // If everything looks fine we do NOT preventDefault() - the form
    // continues on to contact.php, which re-validates everything on the
    // server (never trust the client) and saves the message to MySQL.
    if (!isValid) {
      e.preventDefault();
    }
  });

  function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
  }

  function clearError(elementId) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = '';
  }

});