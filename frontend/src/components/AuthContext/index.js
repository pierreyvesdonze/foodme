import React, { createContext, useContext, useState, useEffect } from 'react';

const AuthContext = createContext();

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
	const [userToken, setUserToken] = useState(sessionStorage.getItem('userToken') || null);
	const [userDetails, setUserDetails] = useState({
		email: sessionStorage.getItem('userEmail') || '',
		pseudo: sessionStorage.getItem('userPseudo') || ''
	});

	const login = (token, email, pseudo) => {
		setUserToken(token);
		sessionStorage.setItem('userToken', token);
		sessionStorage.setItem('userEmail', email);
		sessionStorage.setItem('userPseudo', pseudo);
		setUserDetails({ email, pseudo });
	};

	const logout = () => {
		setUserToken(null);
		sessionStorage.removeItem('userToken');
		sessionStorage.removeItem('userEmail');
		sessionStorage.removeItem('userPseudo');
		setUserDetails({ email: '', pseudo: '' });
		window.location.href = '/login';
	};

	const isLoggedIn = () => !!userToken;

	return (
		<AuthContext.Provider value={{ userToken, userDetails, login, logout, isLoggedIn }}>
			{children}
		</AuthContext.Provider>
	);
};
