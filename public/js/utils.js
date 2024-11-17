window.Utils = {
    getDatetimeLocaleIndo: (date) => {
        let year = date.getFullYear();
        let monthName = date.toLocaleString('id', { month: 'long' });
        let _date = date.getDate().toString().padStart(2, '0');
    
        return `${_date}-${monthName}-${year}`;
    },
    formatPhoneWithDash: (phone) => {
        phone = phone.toString();
    
        return `${phone.substr(0, 3)}-${phone.substr(3, 4)}-${phone.substr(7, 4)}`;
    },
    initializeBootstrapTooltips: () => {
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('.cp_desc_tooltip'));
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    },
    initializePhoneInput: (ele) => {
        let phoneInput = window.intlTelInput(ele, {
            formatOnDisplay: true,
            initialCountry: 'id',
            autoPlaceholder: 'polite',
            placeholderNumberType: 'MOBILE',
            separateDialCode: true,
            strictMode: true,
            validationNumberType: 'MOBILE',
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.3.4/build/js/utils.js",
        });

        return phoneInput;
    }
};