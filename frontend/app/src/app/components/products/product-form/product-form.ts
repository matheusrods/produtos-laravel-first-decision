import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ProductService } from '../../../services/product.service';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { NgxMaskDirective } from 'ngx-mask';

@Component({
  selector: 'app-product-form',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatSnackBarModule,
    NgxMaskDirective   
  ],
  templateUrl: './product-form.html',
  styleUrls: ['./product-form.scss']
})
export class ProductForm implements OnInit {
  id: number | null = null;
  name = '';
  description = '';
  price: number | null = null;
  stock: number | null = null;

  constructor(
    private productService: ProductService,
    private route: ActivatedRoute,
    private router: Router,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));

    if (this.id) {
      this.productService.getById(this.id).subscribe({
        next: (res) => {
          this.name = res.data.name;
          this.description = res.data.description;
          this.price = res.data.price;
          this.stock = res.data.stock;
        },
        error: (err) => {
          console.error(err);
          this.snackBar.open('Erro ao carregar produto.', 'Fechar', {
            duration: 3000,
            panelClass: ['snackbar-error']
          });
        }
      });
    }
  }

  onSubmit(): void {
    // Converte o preço da máscara para número puro
    const priceNumber = this.price
      ? Number(this.price.toString()
        .replace(/\s/g, '')       // remove espaços
        .replace('R$', '')        // remove o prefixo R$
        .replace(/\./g, '')       // remove separadores de milhar
        .replace(',', '.')        // troca vírgula por ponto
      )
      : 0;

    const product = {
      name: this.name,
      description: this.description,
      price: priceNumber,
      stock: this.stock,
    };

    if (this.id) {
      // Atualizar
      this.productService.update(this.id, product).subscribe({
        next: () => {
          this.snackBar.open('Produto atualizado com sucesso!', 'Fechar', {
            duration: 3000,
            panelClass: ['snackbar-success']
          });
          this.router.navigate(['/products']);
        },
        error: (err) => {
          console.error(err);
          this.snackBar.open('Erro ao atualizar produto.', 'Fechar', {
            duration: 3000,
            panelClass: ['snackbar-error']
          });
        }
      });
    } else {
      // Criar
      this.productService.create(product).subscribe({
        next: () => {
          this.snackBar.open('Produto criado com sucesso!', 'Fechar', {
            duration: 3000,
            panelClass: ['snackbar-success']
          });
          this.router.navigate(['/products']);
        },
        error: (err) => {
          console.error(err);
          this.snackBar.open('Erro ao criar produto.', 'Fechar', {
            duration: 3000,
            panelClass: ['snackbar-error']
          });
        }
      });
    }
  }

  voltar() {
    this.router.navigate(['/products']);
  }
}