let antesScrollTop = 0;
navbar = document.getElementById("header");
window.addEventListener("scroll", function(){
    let scrollTop = window.scrollY || document.documentElement.scrollTop;
    if(scrollTop > antesScrollTop){
        navbar.classList.add('ativo');
    } else {
        navbar.classList.remove('ativo');
    }
    antesScrollTop = scrollTop;
})

let index = 0; //variavel que vai ser a imagem que vai estar visivel na hora 
const slides = document.querySelectorAll('.imgCarossel img'); //ele vais selecionar e armazenar todas as imagens com classe .imgCarossel
const totalSlides = slides.length; // uma variavel pra armazenar a quantidade de imaagens que tem 

function galeriaVisivel() {
    document.querySelector('.imgCarossel').style.transform = 'translateX(' + (-index * 100) + '%)';
    // querySelector seleciona o primeiro elemento encontrado, no caso das imagens que tem class imgCarossel
    // o traslate x é pq ele ta momento um elemento no eixo x, ou seja para o lado 
    // o - é pra mover pra esquerda
    //o index x 100 é pq ele descola 100% para o lado 
}

function proximo() {
    if (index < totalSlides -1) { // o -1 é pq a contagem de indicie começa no 0 ai se tirar ele vai 1 a mais
        index++;
        galeriaVisivel();
    }
}

function anterior() {
    if (index > 0) {
        index--;
        galeriaVisivel();
    }
}
AOS.init();