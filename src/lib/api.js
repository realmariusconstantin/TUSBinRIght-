import axios from 'axios';

export default axios.create({
    baseURL: 'http://localhost/tusbinright/public', 
    headers: {
        'Content-Type': 'application/json'
    }
});