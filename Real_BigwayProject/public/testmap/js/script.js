"use strict";

// Function to initialize the map with OpenStreetMap
const initializeMap = () => {
    const defaultLocation = [31.5497, 74.3436]; // Default location: Lahore
    const map = L.map('map').setView(defaultLocation, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 25,
        attribution: 'bitlogicx'
    }).addTo(map);

    return map;
}

// Fetch and parse CSV data
const readCSV = async (csvFilePath) => {
    let result = [];
    await fetch(csvFilePath)
        .then(response => response.text())
        .then(csvText => {
            Papa.parse(csvText, {
                header: true,
                dynamicTyping: true,
                complete: function(results) {
                    result = results.data;
                },
                error: function(error) {
                    console.error('Error parsing CSV file:', error);
                }
            });
        })
        .catch(error => console.error('Error fetching CSV file:', error));
    return result;
}

// Function to calculate optimized routes using OSRM
const calculateOptimizedRoute = async (driver, students, schoolForStudent) => {
    if (!driver || !driver.latitude || !driver.longitude) {
        console.error('Invalid driver location.');
        return [];
    }
    if (!students || students.length === 0) {
        console.error('No students provided for route calculation.');
        return [];
    }

    const waypoints = students
        .filter(student => student.student_pickup_latidute && student.student_pickup_longitude)
        .map(student => ({
            lat: student.student_pickup_latidute,
            lng: student.student_pickup_longitude
        }));

    waypoints.unshift({ lat: driver.latitude, lng: driver.longitude });
    waypoints.push({ lat: schoolForStudent.latitude, lng: schoolForStudent.longitude });

    if (waypoints.length < 2) {
        console.error('Not enough valid waypoints for route calculation.');
        return [];
    }

    const routeUrl = `http://router.project-osrm.org/route/v1/driving/${waypoints.map(p => `${p.lng},${p.lat}`).join(';')}?overview=full&geometries=geojson`;

    try {
        const response = await fetch(routeUrl);
        if (!response.ok) {
            throw new Error(`OSRM API returned status: ${response.status}`);
        }
        const data = await response.json();

        if (data && data.routes && data.routes.length > 0) {
            const routeCoordinates = data.routes[0].geometry.coordinates;
            const routeLatLng = routeCoordinates.map(coord => [coord[1], coord[0]]);
            return routeLatLng;
        } else {
            console.error('No route found:', data);
        }
    } catch (error) {
        console.error('Error fetching route from OSRM API:', error.message);
    }

    return [];
};

// Function to draw route on the map with a specified color
const drawRoute = (map, route, color) => {
    if (route.length > 0) {
        L.polyline(route, { color: color }).addTo(map);
    }
}

// Function to add markers for students on the map
const addStudentMarkers = (map, students) => {
    students.forEach(student => {
        const lat = parseFloat(student.student_pickup_latidute);
        const lon = parseFloat(student.student_pickup_longitude);

        if (!isNaN(lat) && !isNaN(lon)) {
            const customIcon = L.icon({
                iconUrl: 'images/marker-icon.png',
                iconSize: [20, 26],
                iconAnchor: [12, 9],
            });

            L.marker([lat, lon], { icon: customIcon })
                .addTo(map)
                .bindPopup(`<b>${student.student_name}</b><br>Notes: ${student.notes}<br>Drop Time: ${student.drop_time}<br>School ID: ${student.school_id}<br>Pickup Name: ${student.student_pickup_name}<br>Gender: ${student.gender}<br>Parent ID: ${student.parent_id}`);
        } else {
            console.error(`Invalid coordinates for student: ${student.student_name}`);
        }
    });
};

// Function to add markers for drivers on the map
const addDriverMarkers = (map, drivers) => {
    drivers.forEach(driver => {
        const lat = parseFloat(driver.latitude);
        const lon = parseFloat(driver.longitude);

        if (!isNaN(lat) && !isNaN(lon)) {
            const customIcon = L.icon({
                iconUrl: 'images/start.png',
                iconSize: [32, 32],
                iconAnchor: [12, 9],
            });

            L.marker([lat, lon], { icon: customIcon })
                .addTo(map)
                .bindPopup(`<b>${driver.name}</b><br>Vehicle: ${driver.vehicle_number}</b><br>Assigned to: ${driver.driver_id}`);
        } else {
            console.error(`Invalid coordinates for driver: ${driver.name}`);
        }
    });
};

let allRoutes = []; // Store all drawn routes for clearing
let allMarkers = []; // Store all markers for clearing

// Function to filter routes and markers by selected school
const filterRoutesBySchool = (schoolId) => {
    // Clear all existing routes and markers
    allRoutes.forEach(route => {
        if (route) {
            map.removeLayer(route); // Properly remove each route layer
        }
    });
    allMarkers.forEach(marker => {
        if (marker) {
            map.removeLayer(marker); // Properly remove each marker layer
        }
    });

    // Reset arrays
    allRoutes = [];
    allMarkers = [];

    // Filter drivers and students for the selected school
    const filteredDrivers = driverRecords.filter(driver => driver.driver_id === schoolId);
    const filteredStudents = studentRecords.filter(student => student.school_id === schoolId);
    const selectedSchool = schoolRecords.find(school => school.school_id === schoolId);

    // Redraw the markers and routes for the filtered data
    addMarkersAndRoutes(map, filteredDrivers, filteredStudents, [selectedSchool]);
};


// Function to add markers for schools on the map
const addSchoolMarkers = (map, schools) => {
    schools.forEach(school => {
        const lat = parseFloat(school.latitude);
        const lon = parseFloat(school.longitude);

        if (!isNaN(lat) && !isNaN(lon)) {
            const customIcon = L.icon({
                iconUrl: 'images/school.png',
                iconSize: [40, 40],
                iconAnchor: [12, 9],
            });

            L.marker([lat, lon], { icon: customIcon })
                .addTo(map)
                .bindPopup(`<b>${school.school_name}</b><br>Address: ${school.address}<br>School ID:${school.school_id}`);
        } else {
            console.error(`Invalid coordinates for school: ${school.school_name}`);
        }
    });
};

// Function to update lists in the UI
// Function to update lists in the UI and attach event listeners
// Function to update lists in the UI and attach event listeners
const updateList = (elementId, list, key) => {
    let html = '';
    list.forEach(item => {
        html += `
            <button type="button" class="form-control btn btn-outline-secondary mb-1" onclick="filterRoutesBySchool('${item.school_id}')">
                ${item[key]}
            </button>
        `;
    });

    if (list.length === 0) {
        html = 'No entries found';
    }

    document.getElementById(elementId).innerHTML = html;
};



// Function to add markers and calculate routes with different colors
const addMarkersAndRoutes = (map, drivers, students, schoolRecords) => {
    // Add markers and keep track of them
    students.forEach(student => {
        const marker = addStudentMarkers(map, [student]);
        allMarkers.push(marker);
    });

    drivers.forEach(driver => {
        const marker = addDriverMarkers(map, [driver]);
        allMarkers.push(marker);
    });

    schoolRecords.forEach(school => {
        const marker = addSchoolMarkers(map, [school]);
        allMarkers.push(marker);
    });

    const colors = ['red', 'green', 'blue', 'purple', 'orange', 'darkred', 'cadetblue', 'darkblue', 'darkgreen', 'darkpurple'];
    let colorIndex = 0;

    drivers.forEach(async driver => {
        const studentsForDriver = students.filter(student => student.school_id === driver.driver_id);
        const schoolForStudent = schoolRecords.find(school => school.school_id === driver.driver_id);

        if (studentsForDriver.length > 0 && schoolForStudent) {
            const route = await calculateOptimizedRoute(driver, studentsForDriver, schoolForStudent);
            const drawnRoute = drawRoute(map, route, colors[colorIndex]);
            if (drawnRoute) allRoutes.push(drawnRoute);
            colorIndex = (colorIndex + 1) % colors.length;
        }
    });
};



// When the DOM is fully loaded
document.addEventListener('DOMContentLoaded', async () => {
    const map = initializeMap();

    const studentRecords = await readCSV('data/students.csv');
    const driverRecords = await readCSV('data/drivers.csv');
    const schoolRecords = await readCSV('data/schools.csv');

    updateList('studentslist', studentRecords, 'student_name');
    updateList('schoolslist', schoolRecords, 'school_name');

    addMarkersAndRoutes(map, driverRecords, studentRecords, schoolRecords);
});
