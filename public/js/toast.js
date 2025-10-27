window.showToast = function (message, type = 'success') {
    $.toast({
        heading: type.charAt(0).toUpperCase() + type.slice(1), // “Success”, “Error”, etc.
        text: message,
        icon: type, // controls the icon and color style
        position: 'top-right',
        showHideTransition: 'slide',
        loaderBg: '#000', // optional: customize loader color
        hideAfter: 3500,  // auto-hide after 3.5s
    });
};

// ✅ success message
// showToast('Section added successfully.');

// ⚠️ warning message
// showToast('Please fill all required fields.', 'warning');

// ❌ error message
// showToast('Something went wrong!', 'error');

// ℹ️ info message
// showToast('This is just for your information.', 'info');

