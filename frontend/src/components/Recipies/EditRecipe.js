import React, { useEffect, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import axios from 'axios';
import {
    Box,
    Button,
    TextField,
    Typography,
    Container,
    IconButton,
} from '@mui/material';
import { Remove } from '@mui/icons-material';
import baseUrl from '../../baseUrl';
import { useAuth } from '../AuthContext';
import { useNavigate, useParams } from 'react-router-dom';

const EditRecipe = () => {
    const { recipeId } = useParams();
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        portions: '',
        time_prepa: '',
        time_cooking: '',
        image: null,
    });

    const [steps, setSteps] = useState([{ id: null, name: '' }]);
    const [ingredients, setIngredients] = useState([{ id: null, name: '' }]);
    const { userToken } = useAuth();
    const [submitted, setSubmitted] = useState(false);
    const navigate = useNavigate();

    useEffect(() => {
        const fetchRecipe = async () => {
            try {
                const response = await axios.get(`${baseUrl}/api/recipe/${recipeId}`, {
                    headers: {
                        'Authorization': `Bearer ${userToken}`
                    }
                });
                const data = response.data;
                setFormData({
                    title: data.title,
                    description: data.description,
                    portions: data.portions,
                    time_prepa: data.time_prepa,
                    time_cooking: data.time_cooking,
                    image: data.image,
                });
                setSteps(data.steps);
                setIngredients(data.ingredients);
            } catch (error) {
                console.error(error);
            }
        };

        fetchRecipe();
    }, [recipeId, userToken]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleKeyDown = (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        if (submitted) return; // Prevent multiple submissions

        // Vérifier les champs
        const requiredFields = ['title', 'description', 'portions']; 
        const missingFields = requiredFields.filter(field => {
            return !formData[field];
        });
        
        if (missingFields.length > 0) {
            alert(`Veuillez remplir les champs suivants : ${missingFields.join(', ')}`);
            return;
        }

        setSubmitted(true);

        const formDataToSend = new FormData();
        formDataToSend.append('title', formData.title);
        formDataToSend.append('description', formData.description);
        formDataToSend.append('portions', formData.portions);
        formDataToSend.append('time_prepa', formData.time_prepa);
        formDataToSend.append('time_cooking', formData.time_cooking);

        steps.forEach((step, index) => {
            formDataToSend.append(`steps[${index}][id]`, step.id);
            formDataToSend.append(`steps[${index}][name]`, step.name);
        });

        ingredients.forEach((ingredient, index) => {
            formDataToSend.append(`ingredients[${index}][id]`, ingredient.id);
            formDataToSend.append(`ingredients[${index}][name]`, ingredient.name);
        });

        if (formData.image) {
            formDataToSend.append('image', formData.image);
        }

        try {
            await axios.put(`${baseUrl}/api/recipe/edit/${recipeId}`, formDataToSend, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${userToken}`
                }
            });

            navigate(`/recipe/new/image/${recipeId}`);
        } catch (error) {
            console.error(error);
            setSubmitted(false);
        }
    };

    const handleAddStep = () => {
        setSteps([...steps, { id: null, name: '' }]);
    };

    const handleRemoveStep = (index) => {
        const newSteps = steps.filter((_, i) => i !== index);
        setSteps(newSteps);
    };

    const handleStepChange = (index, event) => {
        const newSteps = steps.map((step, i) => {
            if (i === index) {
                return { ...step, [event.target.name]: event.target.value };
            }
            return step;
        });
        setSteps(newSteps);
    };

    const handleAddIngredient = () => {
        setIngredients([...ingredients, { id: null, name: '' }]);
    };

    const handleRemoveIngredient = (index) => {
        const newIngredients = ingredients.filter((_, i) => i !== index);
        setIngredients(newIngredients);
    };

    const handleIngredientChange = (index, event) => {
        const newIngredients = ingredients.map((ingredient, i) => {
            if (i === index) {
                return { ...ingredient, [event.target.name]: event.target.value };
            }
            return ingredient;
        });
        setIngredients(newIngredients);
    };

    return (
        <AnimatePresence>
            <motion.div
                key={'edit_recipe'}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ type: 'easeOut', duration: 1 }}
            >
                <Container style={{ 
                    paddingBottom: '50px',
                    backdropFilter: 'blur(6px)',
                    backgroundColor: '#ffffff12',
                    borderRadius: '20px',
                    padding: '35px',
                }}>
                    <Typography variant="h4" align="center" gutterBottom>
                        Éditer la recette
                    </Typography>
                    <Box component="form" onSubmit={handleSubmit} onKeyDown={handleKeyDown} noValidate>
                        <TextField
                            required
                            fullWidth
                            label="Nom de la recette"
                            name="title"
                            value={formData.title}
                            onChange={handleChange}
                            sx={{ mb: 2 }}
                        />
                        <TextField
                            required
                            fullWidth
                            label="Description courte"
                            name="description"
                            value={formData.description}
                            onChange={handleChange}
                            sx={{ mb: 2 }}
                        />
                        <TextField
                            required
                            fullWidth
                            label="Pour combien de personnes ? (ex. 4)"
                            name="portions"
                            value={formData.portions}
                            onChange={handleChange}
                            sx={{ mb: 2 }}
                        />
                        <TextField
                            required
                            fullWidth
                            label="Temps de préparation (en minutes)"
                            name="time_prepa"
                            type="number"
                            value={formData.time_prepa}
                            onChange={handleChange}
                            sx={{ mb: 2 }}
                        />
                        <TextField
                            required
                            fullWidth
                            label="Temps de cuisson (en minutes)"
                            name="time_cooking"
                            type="number"
                            value={formData.time_cooking}
                            onChange={handleChange}
                            sx={{ mb: 2 }}
                        />

                        <Typography variant="h6">Étapes</Typography>
                        {steps.map((step, index) => (
                            <Box key={index} sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                <TextField
                                    fullWidth
                                    label={`Étape ${index + 1}`}
                                    name="name"
                                    value={step.name}
                                    onChange={(e) => handleStepChange(index, e)}
                                    sx={{ mr: 1 }}
                                />
                                <IconButton onClick={() => handleRemoveStep(index)}>
                                    <Remove />
                                </IconButton>
                            </Box>
                        ))}
                        <Button variant="contained" onClick={handleAddStep} className='custom-btn'>
                            Ajouter une étape
                        </Button>

                        <Typography variant="h6" sx={{ mt: 3 }}>
                            Ingrédients
                        </Typography>

                        {ingredients.map((ingredient, index) => (
                            <Box key={index} sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                <TextField
                                    fullWidth
                                    label={`Ingrédient ${index + 1}`}
                                    name="name"
                                    value={ingredient.name}
                                    onChange={(e) => handleIngredientChange(index, e)}
                                    sx={{ mr: 1 }}
                                />
                                <IconButton onClick={() => handleRemoveIngredient(index)}>
                                    <Remove />
                                </IconButton>
                            </Box>
                        ))}

                        <Button variant="contained" onClick={handleAddIngredient} className='custom-btn'>
                            Ajouter un ingrédient
                        </Button>

                        <br />
                        <br />

                        <Button type="submit" fullWidth variant="contained" sx={{ mt: 3 }} className='custom-btn'>
                            Enregistrer les modifications
                        </Button>
                    </Box>
                </Container>
            </motion.div>
        </AnimatePresence>
    );
};

export default EditRecipe;
