window.Utils = {
    getDatetimeLocaleIndo: (date, showHour = false) => {
        let year = date.getFullYear();
        let monthName = date.toLocaleString('id', { month: 'long' });
        let _date = date.getDate().toString().padStart(2, '0');

        let hours;
        if(showHour){
            hours = ' '+[
                date.getHours().toString().padStart(2, '0'),
                date.getMinutes().toString().padStart(2, '0'),
                // date.getSeconds().toString().padStart(2, '0')
            ].join(':');
        }
    
        return `${_date}-${monthName}-${year}${hours}`;
    },
    simpleDateFormat: (date, showHour = false) => {
        let year = date.getFullYear();
        let month = (date.getMonth()+1).toString().padStart(2, '0');
        let _date = date.getDate().toString().padStart(2, '0');

        let hours;
        if(showHour){
            hours = ' '+[
                date.getHours().toString().padStart(2, '0'),
                date.getMinutes().toString().padStart(2, '0'),
                // date.getSeconds().toString().padStart(2, '0')
            ].join(':');
        }
    
        return `${year}-${month}-${_date}${hours}`;
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