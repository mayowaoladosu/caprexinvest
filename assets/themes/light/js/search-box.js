// Search Box
document.querySelectorAll('.search-box').forEach((searchBox) => {
    const selectOption = searchBox.querySelector('.select-option');
    const soValue = searchBox.querySelector('.soValue');
    const optionSearch = searchBox.querySelector('.optionSearch');
    const options = searchBox.querySelector('.search-options');
    const optionsList = searchBox.querySelectorAll('.search-options li');

    selectOption.addEventListener('click', function(event) {
        searchBox.classList.add('active');
        event.stopPropagation();
    });

    window.addEventListener('click', function () {
        searchBox.classList.remove('active');
    });

    optionsList.forEach(function(optionsListSingle) {
        optionsListSingle.addEventListener('click', function() {
            const text = optionsListSingle.querySelector(".country");
            const textContent = text.textContent;
            soValue.value = textContent;
            searchBox.classList.remove('active');
        });
    });

    optionSearch.addEventListener('keyup', function() {
        var filter, li, i, textValue;
        filter = optionSearch.value.toUpperCase();
        li = options.getElementsByTagName('li');
        for (i = 0; i < li.length; i++) {
            const liCount = li[i];
            textValue = liCount.textContent || liCount.innerText;
            if (textValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = '';
            } else {
                li[i].style.display = 'none';
            }
        }
    });
});
// Search Box