import Nav from './components/Nav';
import { Route, Routes } from 'react-router-dom';
import { AuthProvider } from './components/AuthContext';
import Login from './components/Login';
import Register from './components/Register';
import Home from './components/Home';
import Recipies from './components/Recipies';
import NewRecipe from './components/Recipies/NewRecipe';
import NewRecipeImg from './components/Recipies/NewRecipeImg';
import Recipe from './components/Recipies/Recipe';
import EditRecipe from './components/Recipies/EditRecipe';
import TestComponent from './components/Recipies/TestComponent';

function App() {

	return (
		<div className="App">
			<AuthProvider>
				<Nav />
				<Routes>
					<Route exact path = '/register' element              = {<Register />} />
					<Route exact path = '/login' element                 = {<Login />} />
					<Route exact path = '/home' element                  = {<Home />} />
					<Route exact path = '/recipies' element              = {<Recipies />} />
					<Route exact path = '/recipe/new' element            = {<NewRecipe />} />
					<Route exact path = '/recipe/new/image/:id' element  = {<NewRecipeImg />} />
					<Route exact path = '/recipe/edit/:recipeId' element = {<EditRecipe />} />
					<Route exact path = '/recipe/:recipeId' element      = {<Recipe />} />
					<Route exact path = '/test/:id' element      = {<TestComponent />} />
				</Routes>
			</AuthProvider>
		</div>
	);
}

export default App;
