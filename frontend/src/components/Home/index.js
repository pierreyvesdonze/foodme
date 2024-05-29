import React from 'react';
import { useAuth } from '../AuthContext';
import baseUrl from '../../baseUrl';
import UseUserProfile from '../../hooks/useUserProfile';

const Home = () => {
    const { userToken } = useAuth();
    const username = UseUserProfile(baseUrl, userToken);

    return (
        <div>
            <h1>Bienvenue, {username} !</h1>
        </div>
    );
};

export default Home;
