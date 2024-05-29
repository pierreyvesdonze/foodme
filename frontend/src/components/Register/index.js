import React, { useState } from 'react';
import axios from "axios";
import { useForm, handleSubmit } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import baseUrl from '../../baseUrl';
import { Button, FormControl, FormLabel, Input } from '@mui/material';

export default function Register() {

	const [errorMessage, setErrorMessage] = useState('');
	const { register, handleSubmit, formState: { errors } } = useForm();

	let navigate = useNavigate();

	const onSubmit = data => {

		axios.post(`${baseUrl}/api/register`, data)
			.then((response) => {
				if (response.data.userExist) {
					console.log('user registered')
					alert('Cet email est déjà enregistré.');
					return;
				}
				// Redirection vers la page de connexion après inscription réussie
				navigate("/login");
			})
			.catch((error) => {
				console.error(error);
				// Afficher les messages d'erreur du serveur
				if (error.response && error.response.data) {
					setErrorMessage(error.response.data.message);
				} else {
					setErrorMessage("Une erreur est survenue. Veuillez réessayer.");
				}
			});
	};

	return (
		<main className='centered'>
			<h1 style={{ textAlign: 'center' }}>Créer un compte</h1>
			{errorMessage && <p className="error">{errorMessage}</p>}
			<form onSubmit={handleSubmit(onSubmit)} style={{
				display: 'flex',
				flexDirection: 'column',
				justifyContent: 'center', 
				alignItems: 'center',
				marginTop: '10%',
			}}>
				<FormControl>
					<FormLabel>Email</FormLabel>
					<Input
						name="email"
						type="email"
						placeholder="johndoe@email.com"
						{...register('email', { required: true })}
					/>
					{errors.email && <span className="error">Email requis</span>}
				</FormControl>
				<br />
				<br />
				<FormControl>
					<FormLabel>Pseudo</FormLabel>
					<Input
						name="pseudo"
						type="text"
						placeholder="Choisi un pseudo"
						{...register('pseudo', { required: true })}
					/>
					{errors.email && <span className="error">Pseudo requis</span>}
				</FormControl>
				<br />
				<br />
				<FormControl>
					<FormLabel>Mot de passe</FormLabel>
					<Input
						name="password"
						type="password"
						placeholder="mot de passe"
						{...register('password', { required: true })}
					/>
					{errors.password && <span className="error">Mot de passe est requis</span>}
				</FormControl>
				<br /><br />
				<Button
					variant="soft"
					color="neutral"
					sx={{ mt: 1 }} // margin top
					type="submit"
				>
					Créer un compte
				</Button>
			</form>
		</main>
	);
};
