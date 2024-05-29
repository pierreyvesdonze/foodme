import * as React from 'react';
import Box from '@mui/material/Box';
import Drawer from '@mui/material/Drawer';
import Button from '@mui/material/Button';
import List from '@mui/material/List';
import Divider from '@mui/material/Divider';
import MenuIcon from '@mui/icons-material/Menu';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemText from '@mui/material/ListItemText';
import { Link } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function TemporaryDrawer() {
    const [open, setOpen] = React.useState(false);
    const { isLoggedIn, logout } = useAuth(); // Récupérez les fonctions nécessaires du contexte

    const toggleDrawer = (newOpen) => () => {
        setOpen(newOpen);
    };

    const DrawerList = (
        <Box sx={{ width: 250, backgroundColor: 'transparent' }} role="presentation" onClick={toggleDrawer(false)} className='navbar'>
            <List>
                <Link to={'/recipies'}>
                    <ListItemButton>
                        Recettes
                    </ListItemButton>
                </Link>
            </List>
            <Divider />
            <List>
                {isLoggedIn() ? (
                    <>
                        <Link to={'/recipe/new'}>
                            <ListItemButton>
                                Créer une recette
                            </ListItemButton>
                        </Link>
                        <ListItemButton onClick={logout}>
                            <ListItemText primary="Déconnexion" />
                        </ListItemButton>
                    </>
                ) : (
                    <>
                        <ListItemButton>
                            <Link to={'/login'}>Connexion</Link>
                        </ListItemButton>
                        <ListItemButton>
                            <Link to={'/register'}>Créer un compte</Link>
                        </ListItemButton>
                    </>
                )}
            </List>
        </Box>
    );

    return (
        <div>
            <Button onClick={toggleDrawer(true)}>
                <MenuIcon style={{ color: 'black' }} />
            </Button>
            <Drawer open={open} onClose={toggleDrawer(false)}>
                {DrawerList}
            </Drawer>
        </div>
    );
}
