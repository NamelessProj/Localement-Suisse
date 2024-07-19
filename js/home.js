let splide1 = new Splide('#splide1', {
    perPage: 3,
    gap: '1rem',
    omitEnd: true,
    breakpoints: {
        1200: { perPage: 2, gap: '1rem' },
        640 : { perPage: 1, gap: 0 },
    },
});
let splide2 = new Splide('#splide2', {
    perPage: 3,
    gap: '1rem',
    omitEnd: true,
    breakpoints: {
        1200: { perPage: 2, gap: '1rem' },
        640 : { perPage: 1, gap: 0 },
    },
});

splide1.mount();
splide2.mount();