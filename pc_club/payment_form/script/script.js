document.addEventListener("DOMContentLoaded", function () {
    const cardNumberInput = document.querySelector('#card-number-input');
    const cardHolderInput = document.getElementById('card-holder-input');
    const cardExpiryMonth = document.getElementById('card-expiry-month');
    const cardExpiryYear = document.getElementById('card-expiry-year');
    const cardCvvInput = document.getElementById('card-cvv-input');

    const cardNumberDisplay = document.getElementById('card-number');
    const cardHolderDisplay = document.getElementById('card-holder');
    const cardExpiryDisplay = document.getElementById('expiry');
    const cardExpiry = document.getElementById('card-expiry');
    const cvvHolderDisplay = document.querySelector('#cvv > h2');
    const revImgCvv = document.querySelector('.card-brand-rev')
    const imgMoveDiv = document.querySelector('.logo-cards > div > div')
    const cardHolderDisplay2 = document.getElementById('holder');

    const digits = document.querySelectorAll('.digit span');
    let previousValue = "";
    
    
        cardNumberInput.addEventListener('input',  ev => {
            console.log(digits[0].textContent);
            if (digits[0].textContent !== "#") {
                switch (digits[0].textContent) {
                    case ('5'):
                        imgMoveDiv.style.marginTop = '0px'
                        revImgCvv.src = "img/card-type/mastercard.png"
                        break;
                    case ('4'):
                        imgMoveDiv.style.marginTop = '-100px'
                        revImgCvv.src = "img/card-type/visa.png"
                        break;
                    case ('6'):
                        imgMoveDiv.style.marginTop = '-170px'
                        revImgCvv.src = "img/card-type/discover.png"
                        break;
                    case ('3'):
                        imgMoveDiv.style.marginTop = '-230px'
                        revImgCvv.src = "img/card-type/amex.png"
                        break;
                }
            }

            const input = cardNumberInput.value;
            let filteredInput = "";
            for (let i = 0; i < input.length; i++) {
                const char = input[i];
                if (!isNaN(char) && char !== " ") {
                    filteredInput += char;
                }
            }
            cardNumberInput.value = filteredInput;
        
        
            filteredInput.split('').forEach((char, index) => {
                const digit = digits[index];  
        
                if (digit && char !== previousValue[index]) {
                    digit.style.transform = `translateY(-100%)`;  
                    setTimeout(() => {
                        digit.textContent = char; 
                        digit.style.transform = `translateY(0)`; 
                    }, 300);
                }
            });
        
            for (let i = filteredInput.length; i < digits.length; i++) {
                const digit = digits[i];
                if (digit && digit.textContent !== '#') {
                    digit.style.transform = `translateY(-100%)`;
                    setTimeout(() => {
                        digit.textContent = '#'; 
                        digit.style.transform = `translateY(0)`;
                    }, 300);
                }
            }
            previousValue = filteredInput;
        });
        

       
    cardNumberInput.addEventListener('click', () => {
        cardNumberDisplay.classList.add('marking')
        cardHolderDisplay2.classList.remove('marking')
        cardExpiryDisplay.classList.remove('marking')

    })
    cardHolderInput.addEventListener('click', () => {
        cardHolderDisplay2.classList.add('marking')
        cardNumberDisplay.classList.remove('marking')
        cardExpiryDisplay.classList.remove('marking')
    })

    cardHolderInput.addEventListener('input', function () {
        cardHolderDisplay.textContent = this.value.toUpperCase() || "Card Holder";
    });

    cardExpiryMonth.addEventListener('change', updateExpiryDate);
    cardExpiryYear.addEventListener('change', updateExpiryDate);

    cardCvvInput.addEventListener('focus', function () {
        document.querySelector('.card').style.transform = 'rotateY(180deg)';
        document.querySelector('.cardBack').style.transform = 'rotateY(0deg)';
    });

    cardCvvInput.addEventListener('blur', function () {
        document.querySelector('.card').style.transform = 'rotateY(0deg)';
        document.querySelector('.cardBack').style.transform = 'rotateY(180deg)';
    });
    cardCvvInput.addEventListener('input', function () {
        cvvHolderDisplay.innerHTML = cardCvvInput.value

    });

    cardExpiryMonth.addEventListener('click', () => {
        cardExpiryDisplay.classList.add('marking')
        cardNumberDisplay.classList.remove('marking')
        cardHolderDisplay.classList.remove('marking')
    })

    cardExpiryYear.addEventListener('click', () => {
        cardExpiryDisplay.classList.add('marking')
        cardNumberDisplay.classList.remove('marking')
        cardHolderDisplay.classList.remove('marking')
    })


function updateExpiryDate  ()   {
        const month = cardExpiryMonth.value === "MM" ? "MM" : cardExpiryMonth.value;
        const year = cardExpiryYear.value === "YY" ? "YY" : cardExpiryYear.value;
        cardExpiry.textContent = `${month}/${year}`;
    } });
    