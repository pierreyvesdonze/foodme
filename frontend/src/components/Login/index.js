import React from 'react';
import axios from "axios";
import { useForm } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';
import baseUrl from '../../baseUrl';
import { Button, FormControl, FormLabel, Input } from '@mui/material';

export default function Login() {
    const { login } = useAuth();
    const { register, handleSubmit, formState: { errors } } = useForm();

    let navigate = useNavigate();

    const onSubmit = data => {
        axios.post(`${baseUrl}/api/login`, data)
            .then((response) => {
                const { token, email, pseudo } = response.data;
                login(token, email, pseudo); // Ajout des détails de l'utilisateur lors de la connexion
                navigate("/home");
            })
            .catch((error) => {
                console.error(error);
            });
    };

    return (
        <main className='centered'>
            <h1 style={{ textAlign: 'center' }}>Connexion</h1>
            <form onSubmit={handleSubmit(onSubmit)} style={{
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center', 
                alignItems: 'center',
                marginTop: '20%',
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
                <br /><br />
                <FormControl>
                    <FormLabel>Mot de passe</FormLabel>
                    <Input
                        name="password"
                        type="password"
                        placeholder="mot de passe requis"
                        {...register('password', { required: true })}
                    />
                    {errors.password && <span className="error">Mot de passe requis</span>}
                </FormControl>
                <br /><br />
                <Button
                    variant="soft"
                    color="neutral"
                    sx={{ mt: 1 }} // margin top
                    type="submit"
                >
                    Connexion
                </Button>
            </form>
        </main>
    );
}
