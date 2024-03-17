import Chart from 'chart.js/auto'

window.addEventListener("DOMContentLoaded", () => {
    (async function() {

        const months = {
            "1": "Jan",
            "2": "Feb",
            "3": "Mar",
            "4": "Apr",
            "5": "May",
            "6": "Jun",
            "7": "Jul",
            "8": "Aug",
            "9": "Sep",
            "10": "Oct",
            "11": "Nov",
            "12": "Dec"
          };


        const parsedData = JSON.parse(document.querySelector("[data-everyMonth]").getAttribute('data-everyMonth'))
        console.log(parsedData)
        const data = Object.keys(parsedData).map(month => ({
            month: months[month],
            value: parseInt(parsedData[month])
        }));

      
        new Chart(
            document.querySelector('#revenueChart'),
            {
              type: 'bar',
              data: {
                labels: data.map(row => row.month),
                datasets: [
                  {
                    label: 'Revenue mensuel',
                    data: data.map(row => row.value)
                  }
                ]
              }
            }
          );
      })();
})