import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Link, useParams } from 'react-router-dom';
import { Box, Card, CardContent, CardMedia, Grid, Typography, CardActions, Divider, Fab } from '@mui/material';
import baseUrl from '../../baseUrl';
import { useAuth } from '../AuthContext';
import { jwtDecode } from "jwt-decode";
import Loading from '../Loading';
import AddShoppingCartIcon from '@mui/icons-material/AddShoppingCart';
import EditIcon from '@mui/icons-material/Edit';

const Recipe = () => {

    const [recipe, setRecipe] = useState(null);
    const { recipeId } = useParams();
    const { userToken } = useAuth();
    let recipeUser;

    if (userToken) {
        const decodedToken = jwtDecode(userToken)
        recipeUser = decodedToken.username;
    }

    useEffect(() => {
        const fetchRecipe = async () => {
            try {
                const response = await axios.get(`${baseUrl}/api/recipe/${recipeId}`);
                setRecipe(response.data);
            } catch (error) {
                console.error('Error fetching recipe:', error);
            }
        };

        fetchRecipe();
    }, [recipeId]);

    return (
        <Box p={2}>
            {recipe ? (
                <Grid container spacing={2}>
                    <Grid item xs={12} md={6}>
                        <Card>
                            <CardMedia
                                component="img"
                                height="560"
                                image={`${baseUrl}${recipe.image}`}
                                alt={recipe.title}
                            />
                        </Card>
                    </Grid>
                    <Grid item xs={12} md={6}>
                        <Card>
                            <CardContent>
                                <Typography gutterBottom variant="h5" component="div">
                                    {recipe.title}
                                </Typography>
                                <Typography variant="body2" color="text.secondary">
                                    {recipe.description}
                                </Typography>
                                <br />
                                <Typography variant="span" color="text.secondary">
                                    Portions: {recipe.portions}
                                </Typography>
                                <Typography variant="body2" color="text.secondary">
                                    Temps de préparation: {recipe.time_prepa} minutes
                                </Typography>
                                <Typography variant="body2" color="text.secondary">
                                    Temps de cuisson: {recipe.time_cooking} minutes
                                </Typography>
                                <br />
                                <Typography variant="body2" color="text.secondary">
                                    Ingrédients:
                                    <ul>
                                        {recipe.ingredients.map(ingredient => (
                                            <li key={ingredient.id}>{ingredient.name}</li>
                                        ))}
                                    </ul>
                                </Typography>
                                <br />
                                <Typography variant="body2" color="text.secondary">
                                    Étapes:
                                    <ul>
                                        {recipe.steps.map((step, index) => (
                                            <li key={step.id}>{`${index + 1}. ${step.name}`}</li>
                                        ))}
                                    </ul>
                                </Typography>
                            </CardContent>
                            <Divider />
                            <CardActions sx={{ padding: '20px' }}>
                                <Link to={'/recipies'}>
                                    <Fab variant="extended">
                                        Retour
                                    </Fab>
                                </Link>

                                <Fab variant="extended">
                                    <AddShoppingCartIcon />
                                </Fab>

                                {recipe.username === recipeUser && (
                                    <Link to={`/recipe/edit/${recipeId}`}>
                                        <Fab variant="extended">
                                            <EditIcon />
                                        </Fab>
                                    </Link>
                                )}
                            </CardActions>
                        </Card>
                    </Grid>
                </Grid>
            ) : (
                <Loading />
            )}
        </Box>
    );
};

export default Recipe;
