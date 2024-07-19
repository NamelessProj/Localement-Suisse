// Load blur image

const blurDivs = document.querySelectorAll('.blur-load');
blurDivs.forEach(div => {
    const img = div.querySelector('img');

    function loaded(){
        div.classList.add('loaded');
    }

    if(img.complete){
        loaded();
    }else{
        img.addEventListener('load', loaded());
    }
});



// ACCORDEON

const accordeonItems = document.querySelectorAll('.accordeon .item');
accordeonItems.forEach(item => {
    item.addEventListener('click', () => {
        console.log(555);
        document.querySelector('.accordeon .item.open').classList.remove('open');
        item.classList.add('open');
    });
});



// DROPDOWN

const selectedAll = document.querySelectorAll(".wrapper-dropdown");

selectedAll.forEach((selected) => {
    const optionsContainer = selected.children[2];
    const optionsList = selected.querySelectorAll(".wrapper-dropdown li");

    selected.addEventListener("click", () => {
        let arrow = selected.children[1];

        if(selected.classList.contains("active")){
            handleDropdown(selected, arrow, false);
        }else{
            let currentActive = document.querySelector(".wrapper-dropdown.active");

            if(currentActive){
                let anotherArrow = currentActive.children[1];
                handleDropdown(currentActive, anotherArrow, false);
            }

            handleDropdown(selected, arrow, true);

            optionsList.forEach(optL => {
                optL.setAttribute('tabindex', '0');
            });
        }
    });

    // Update the display of dropdown
    for(let o of optionsList){
        o.addEventListener("click", () => {
            let option = o.innerText;
            selected.querySelector(".selected-display").innerText = option;
            window.location.href = `./languageChanger.php?lang=${option.toLocaleLowerCase()}`;
        });
    }
});

// Check if anything else after than the dropdown is clicked
/* window.addEventListener("click", function(e){
    if(e.target.closest(".wrapper-dropdown") === null){
        closeAllDropdowns();
    }
}); */

// Close all dropdowns
function closeAllDropdowns(){
    const selectedAll = document.querySelectorAll(".wrapper-dropdown");
    selectedAll.forEach((selected) => {
        const optionsContainer = selected.children[2];
        let arrow = selected.children[1];

        handleDropdown(selected, arrow, false);
    });
}

// Open all dropdowns
function handleDropdown(dropdown, arrow, open){
    if(open){
        arrow.classList.add("rotated");
        dropdown.classList.add("active");
    }else{
        arrow.classList.remove("rotated");
        dropdown.classList.remove("active");
    }
}



// NAV

const navbar = document.querySelector('nav.navbar');
const navbarCollapse = document.querySelector('nav.navbar .navbar-collapse');
const navbarHeight = navbar.clientHeight;
let prevScrollPos = window.pageYOffset;

window.onscroll = () => {
    navbar.classList.toggle('scroll', window.scrollY > 100);

    let currentScrollPos = window.pageYOffset;
    if(prevScrollPos > currentScrollPos){
        navbar.style.top = "0";
    }else{
        navbar.style.top = "-"+navbarHeight+"px";
    }

    if(window.innerWidth > 1200){
        if(prevScrollPos > currentScrollPos){
            navbarCollapse.style.top = navbarHeight+"px";
        }else{
            navbarCollapse.style.top = "0";
        }
    }

    prevScrollPos = currentScrollPos;
}



// menu hamburger

const navbarBtn = document.querySelector('nav.navbar button.navbar-toggle');

navbarBtn.addEventListener('click', () => {
    let target = document.querySelector(navbarBtn.dataset.target);

    if(navbarBtn.classList.contains('visible')){
        navbarBtn.classList.remove('visible');
        target.classList.remove('visible');
    }else{
        navbarBtn.classList.add('visible');
        target.classList.add('visible');
    }
});



// search navbar

const inputSearchNav = document.querySelector('#search-nav');
const xSearchNav = document.querySelector('button.search-cross');
inputSearchNav.addEventListener('input', () => {
    if(inputSearchNav.value !== ''){
        xSearchNav.classList.add('visible');
    }else{
        xSearchNav.classList.remove('visible');
    }
});
inputSearchNav.addEventListener('blur', () => {
    xSearchNav.classList.remove('visible');
});
inputSearchNav.addEventListener('focus', () => {
    if(inputSearchNav.value !== ''){
        xSearchNav.classList.add('visible');
    }
});

xSearchNav.addEventListener('click', () => {
    inputSearchNav.value = '';
});



// Check when click

window.addEventListener('click', function(e){
    // Check if anything else after than the dropdown is clicked
    if(e.target.closest(".wrapper-dropdown") === null){
        closeAllDropdowns();
    }

    // Check if click outside of navmenu
    if(e.target.closest('nav.navbar button.navbar-toggle') === null){
        var target = document.querySelector(navbarBtn.dataset.target);
        navbarBtn.classList.remove('visible');
        target.classList.remove('visible');
    }
});



// Form
if(document.querySelector('form')){
    const allInputs = document.querySelectorAll('input');
    allInputs.forEach(elem => {
        elem.addEventListener('change', () => {
            elem.classList.toggle('not-empty', elem.value !== '');
        });
    });
}



// Notifications
const allNotifications = document.querySelectorAll('.notification');
let timer = 3000;
allNotifications.forEach(notif => {
    setTimeout(() => {
        notif.classList.add('read');
    }, timer);
    timer += 2500;
});



// AJAX SEARCH BAR TODO: DELETE
$("document").ready(()=>{
    $("#search-nav").keyup(()=>{
        let list = $("#search-nav").val();
        $("#productsDatalist").empty();
        if(list!=""){
            $("#productsDatalist").innerHTML = '';
            $.getJSON("./components/ajax.php?search="+list,(data)=>{
                $.each(data,(k,v)=>{
                    $("#productsDatalist").append('<option value="'+v['pro_name']+'"></option>');
                });
            });
        }
    });
});