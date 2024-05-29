import { useParams } from 'react-router-dom';

const TestComponent = () => {
    const { id } = useParams();
    console.log(id)
    console.log('pouet')
    return('prout')
    
}

export default TestComponent;