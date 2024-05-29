import React, { useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import axios from 'axios';
import {
    Box,
    Button,
    Typography,
    Container,
    Input,
} from '@mui/material';
import { useNavigate, useParams } from 'react-router-dom';

import baseUrl from '../../baseUrl';
import { useAuth } from '../AuthContext';

const NewRecipeImg = () => {
    const [formData, setFormData] = useState({ image: null });
    const { userToken } = useAuth();
    const [submitted, setSubmitted] = useState(false);
    const { id } = useParams();
    const navigate = useNavigate();

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        setFormData({ ...formData, image: file });
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        if (submitted) return; // Prevent multiple submissions
        setSubmitted(true);

        const formDataToSend = new FormData();
        if (formData.image) {
            formDataToSend.append('image', formData.image);
        }

        try {
            const response = await axios.post(`${baseUrl}/api/recipe/new/image/${id}`, formDataToSend, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': `Bearer ${userToken}`
                }
            });
            console.log(response.data);
            navigate(`/recipe/${id}`);
        } catch (error) {
            console.error(error);
            setSubmitted(false); // Allow resubmission if there was an error
        }
    };

    return (
        <AnimatePresence>
            <motion.div
                key={'absurdum'}
                initial={{ left: '-800px' }}
                animate={{ left: 0 }}
                transition={{ type: 'easeOut', duration: 1 }}
            >
                <Container>
                    <Typography variant="h4" align="center" gutterBottom>
                        Ajouter une image
                    </Typography>
                    <Box component="form" onSubmit={handleSubmit} noValidate>
                        <Button
                            variant="contained"
                            component="label"
                            htmlFor="imageInput"
                            sx={{ mb: 2 }}
                            className='custom-btn'
                        >
                            Uploader une image
                            <Input
                                id="imageInput"
                                type="file"
                                accept="image/*"
                                onChange={handleImageChange}
                                style={{ display: 'none' }}
                            />
                        </Button>

                        <Button type="submit" fullWidth variant="contained" className='custom-btn' sx={{ mt: 3 }}>
                            Enregistrer l'image
                        </Button>

                        <Button
                            type="button"
                            fullWidth
                            variant="contained"
                            className='custom-btn'
                            sx={{ mt: 3 }}
                            onClick={() => navigate(`/recipe/${id}`)}
                        >
                            Ignorer l'image
                        </Button>
                    </Box>
                </Container>
            </motion.div>
        </AnimatePresence>
    );
};

export default NewRecipeImg;
