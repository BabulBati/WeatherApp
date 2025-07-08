let locationSearch = document.getElementById('location_search');
const btnSearch = document.getElementById('search');

function checkInput() {
    if (locationSearch.value.length !== 0) {
        fetchWeather(locationSearch.value);
    } else {
        alert("Enter valid value");
    }
}

btnSearch.addEventListener('click', () => checkInput());

locationSearch.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        checkInput();
    }
});

async function fetchWeather(locationName) {
    let city = locationName ? locationName : "Ozark";

    if (!navigator.onLine) {
        const storedData = localStorage.getItem(city.toLowerCase());
        if (storedData) {
            displayWeather(JSON.parse(storedData));
        } else {
            alert("No internet connection and no stored data available.");
        }
        return;
    }

    const url = `http://localhost/Prototype2/BabulKrishnaBati_2548399_connection.php?cityName=${city}`;

    try {
        const response = await fetch(url);
        const data = await response.json();

        if (data.message) {
            throw new Error(data.message);
        } else {
            localStorage.setItem(data.city_name.toLowerCase(), JSON.stringify(data));
            displayWeather(data);
        }
    } catch (error) {
        alert(error.message);
    }
}

function displayWeather(data) {
    let iconName = `https://openweathermap.org/img/wn/${data.weather_icon}@2x.png`;
    document.getElementById('weather_icon').src = iconName;
    document.getElementById('city_name').innerHTML = data.city_name;
    document.getElementById('weather_main').innerHTML = data.weather_main;
    document.getElementById('weather_condition').innerHTML = data.weather_condition;
    document.getElementById('temp').innerHTML = parseFloat(data.temp);
    document.getElementById('pressure').innerHTML = parseFloat(data.pressure);
    document.getElementById('humidity').innerHTML = parseFloat(data.humidity);
    document.getElementById('wind_speed').innerHTML = parseFloat(data.wind_speed);
    document.getElementById('wind_direction').innerHTML = parseFloat(data.wind_direction);

    document.getElementById('wind_direction-icon').style.transform = `rotate(${data.wind_direction}deg)`;

    let localTime = new Date((parseInt(data.city_dt) + parseInt(data.timezone)) * 1000);

    let timeFormat = localTime.toLocaleString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'UTC'
    });

    let dateFormat = localTime.toLocaleString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        timeZone: 'UTC'
    });

    document.getElementById('time-city').innerHTML = timeFormat;
    document.getElementById('date-city').innerHTML = dateFormat;
}

document.addEventListener("DOMContentLoaded", () => fetchWeather());
