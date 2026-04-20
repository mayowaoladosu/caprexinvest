
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const addToCart = (id,quantity,route)=>{
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _token: csrfToken,
            product_id: id,
            quantity: quantity === 0 ? 1 :quantity,
        }),
    })
        .then(response => response.text()) // Parse as text to debug
        .then(resData => {
            try {
                const data = JSON.parse(resData);
               const cartItems = data.data;
                let html = '';
                Object.keys(cartItems).forEach((key)=>{

                    html += `<li class="search-bag-items" id="cartItem${cartItems[key].id}">
                        <div class="search-bag-content">
                            <div class="search-bag-image">
                                <img src="${cartItems[key].image}" alt="product">
                            </div>
                            <div class="search-bag-title">
                                <h6>${cartItems[key].name} </h6>
                                <div class="search-bag-count">
                                    <p>${currencyPosition(cartItems[key].price)}/${cartItems[key].product_quantity }gm</p>
                                    <div class="incriment-dicriment">
                                        <div class="count-single">
                                            <button type="button"  class="decrement" data-id="${cartItems[key].id}"><i class="fa-light fa-minus"></i></button>
                                            <span class="number quantity" id="no${cartItems[key].id}">${cartItems[key].quantity}</span>
                                            <button type="button" class="increment" data-id="${cartItems[key].id}"><i class="fa-light fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="serch-bag-amount">
                            <h6>${currencyPosition(cartItems[key].quantity * cartItems[key].price)}</h6>
                        </div>
                        <div class="serch-bag-close">
                            <div class="close-btn removeCartItem" data-id="${cartItems[key].id}">
                                <i class="fa-regular fa-xmark"></i>
                            </div>
                        </div>
                    </li>`
                })
                document.getElementById('cartItems').innerHTML = html;
                cartLabel(cartItems)
            } catch (e) {
                console.error('Invalid JSON:', e);
            }
        })
        .catch(error => console.error('Error:', error));

}
function cartLabel(data) {
    const cartData = Object.values(data);
    Array.from(document.querySelectorAll('.totalCartItems')).forEach((item)=>{
        item.innerText = cartData.length;
    })
}



