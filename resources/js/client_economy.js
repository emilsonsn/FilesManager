$(document).ready(function () {
    var ctx = document.getElementById('milhasChart').getContext('2d');
    var myChart;

    function updateChart() {
        const id = getParameterByName('client_id');
        const start_date = getParameterByName('start_date');
        const end_date = getParameterByName('end_date');

        if (id && start_date && end_date) {
            let endpoint = `/milespoints/${id}?start_date=${start_date}&end_date=${end_date}`;

            fetch(endpoint)
                .then(response => response.json())
                .then(data => {
                    const datasets = [];
                    let programDates = []; // Definindo programDates fora do loop

                    console.log('d')

                    $('#percentage strong').html(data.average_growth.toFixed(2) + '%');

                    $('.programs').empty(); // Limpar conteúdo anterior

                    $('.programs').append(`<h5 class="pt-1"><i class="fa-solid fa-chart-line"></i> <span class="ms-2">Quantidade média de aumento em cada programa no périodo</span></h5>`);

                    for (const program in data.details) {
                        const programData = data.details[program]['logs'].map(item => parseFloat(item.quantity));
                        const growthData = data.growth[program]['growth'];
                        const percentageGrowth = data.growth[program]['percentage'];

                        const currentProgramDates = data.details[program]['logs'].map(item => new Date(item.created_at).toLocaleDateString('pt-BR'));

                        // Verifica se programDates está vazio e o preenche
                        if (!programDates.length) {
                            programDates = currentProgramDates;
                        }

                        datasets.push({
                            label: `${program} - Crescimento de Milhas`,
                            data: programData,
                            backgroundColor: getRandomColor(0.2),
                            borderColor: getRandomColor(1),
                            borderWidth: 1
                        });

                        $('.programs').append(`
                            <div class="program-info col-md-3">
                            <strong>${program}</strong>
                            <p>${growthData.toLocaleString('pt-BR')}<br>(${percentageGrowth.toFixed(2)}%)</p>
                            </div>
                        `);
                    }

                    $('.programs').append(`<h5 class="pt-1"><i class="fa-solid fa-circle-info"></i> <span class="ms-2">Quantidade atual de milhas</span></h5>`);

                    for (const program in data.current_value) {
                        const growthData = data.current_value[program];
                        $('.programs').append(`
                            <div class="program-info col-md-3">
                            <strong>${program}</strong>
                            <p>${growthData.toLocaleString('pt-BR')}</p>
                            </div>
                        `);
                    }

                    if (myChart) {
                        myChart.destroy(); // Destruir o gráfico antigo antes de criar um novo
                    }

                    const colors = [
                        'rgba(255, 99, 132, 0.8)',   // Vermelho
                        'rgba(54, 162, 235, 0.8)',   // Azul
                        'rgba(255, 206, 86, 0.8)',   // Amarelo
                        'rgba(75, 192, 192, 0.8)',   // Verde-água
                        'rgba(153, 102, 255, 0.8)',  // Roxo
                        'rgba(255, 159, 64, 0.8)',   // Laranja
                        'rgba(233, 30, 99, 0.8)',    // Rosa
                        'rgba(63, 81, 181, 0.8)'     // Índigo
                    ];

myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: programDates,
        datasets: datasets.map(dataset => ({
            ...dataset,
            backgroundColor: generateRandomColor(0.8), // Cor de fundo
            borderColor: generateRandomColor(3),
            borderWidth: 2,
            pointBackgroundColor: 'white',
            pointBorderColor: 'rgba(255, 99, 132, 0.8)',
            pointRadius: 3,
            fill: false,
            tension: 0.4 // Suavidade da linha
        }))
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true, // Mostrar legenda
                position: 'top', // Posição da legenda
            },
            tooltip: {
                enabled: true,
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: true,
                    color: 'rgba(200, 200, 200, 0.3)', // Cor das linhas de grade
                    borderDash: [2, 2], // Estilo das linhas de grade
                },
                title: {
                    display: true,
                    text: 'Quantidade de Milhas', // Título do eixo Y
                    color: '#191919'
                }
            },
            x: {
                grid: {
                    display: false // Ocultar linhas de grade no eixo X
                },
                title: {
                    display: true,
                    text: 'Data', // Título do eixo X
                    color: '#191919'
                }
            }
        },
        animation: {
            duration: 1500, // Tempo de animação
            easing: 'easeOutCubic', // Tipo de animação
        }
    }
});

                })
                .catch(error => console.error('Erro ao buscar dados:', error));
        }
    }

    function generateRandomColor(opacity) {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }


    function getRandomColor(opacity) {
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    updateChart(); // Chamar função ao carregar
});
