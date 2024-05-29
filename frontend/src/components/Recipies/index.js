import React, { useState, useEffect } from 'react';
import axios from 'axios';
import baseUrl from '../../baseUrl';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import { CardActionArea, Grid } from '@mui/material';
import { AnimatePresence, motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import Loading from '../Loading';

const Recipies = () => {
    const [recipies, setRecipies] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchRecipies = async () => {
            try {
                const response = await axios.get(`${baseUrl}/api/recipies`);
                setRecipies(response.data);
            } catch (err) {
                setError(err);
            } finally {
                setLoading(false);
            }
        };

        fetchRecipies();
    }, []);

    if (loading) return <Loading />;
    if (error) return <p>Error loading recipes: {error.message}</p>;

    return (
        <div>
            <h1 style={{ textAlign: 'center' }}>Recettes</h1>

            <AnimatePresence>
                <Grid container spacing={2}>
                    {recipies.map((recipe, index) => (
                        <Grid item xs={12} sm={6} md={4} lg={3} key={recipe.id} sx={{ display: 'flex', justifyContent: 'center' }}>
                            <motion.div
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                transition={{ type: 'easeOut', duration: 0.5, delay: index * 0.1 }}
                            >
                                <Link to={`/recipe/${recipe.id}`}>
                                    <Card sx={{ maxWidth: 345, minWidth: 300 }}>
                                        <CardActionArea>
                                            <CardMedia
                                                component="img"
                                                height="140"
                                                image={`${baseUrl}${recipe.image}`}
                                                alt="recette"
                                            />
                                            <CardContent>
                                                <Typography gutterBottom variant="h5" component="div">
                                                    {recipe.title}
                                                </Typography>
                                                <Typography variant="body2" color="text.secondary">
                                                    {recipe.description}
                                                </Typography>
                                            </CardContent>
                                        </CardActionArea>
                                    </Card>
                                </Link>
                            </motion.div>
                        </Grid>
                    ))}
                </Grid>
            </AnimatePresence>
        </div>
    );
};

export default Recipies;
