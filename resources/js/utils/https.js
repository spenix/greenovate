import axios from 'axios'

const http = axios.create({
    // timeout: 5000
})

http.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status >= 500) {
            console.error(error.message)
        }
        return Promise.reject(error)
    }
)

export default http