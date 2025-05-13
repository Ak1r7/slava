document.addEventListener('DOMContentLoaded', function() {

    const mobileMenuButton = document.createElement('button');
    mobileMenuButton.className = 'mobile-menu-button';
    mobileMenuButton.innerHTML = '<i class="fas fa-bars"></i>';
    
    const header = document.querySelector('header .container');
    if (header) {
        header.prepend(mobileMenuButton);
        
        const nav = document.querySelector('nav');
        mobileMenuButton.addEventListener('click', function() {
            nav.style.display = nav.style.display === 'block' ? 'none' : 'block';
        });
        
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                nav.style.display = '';
            }
        });
    }
    
    const deleteButtons = document.querySelectorAll('.btn.danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
                e.preventDefault();
            }
        });
    });
    
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const numberInputs = form.querySelectorAll('input[type="number"]');
            numberInputs.forEach(input => {
                if (input.value <= 0) {
                    alert('Количество должно быть больше нуля');
                    e.preventDefault();
                    return;
                }
            });
        });
    });
});