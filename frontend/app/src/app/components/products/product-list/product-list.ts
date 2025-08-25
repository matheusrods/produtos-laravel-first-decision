import { Component, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatPaginator, MatPaginatorModule } from '@angular/material/paginator';
import { MatSort, MatSortModule } from '@angular/material/sort';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { ProductService } from '../../../services/product.service';
import { Router } from '@angular/router';
import { ConfirmDialog } from '../../../shared/confirm-dialog/confirm-dialog';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatPaginatorModule,
    MatSortModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule,
    MatButtonModule,
    MatToolbarModule,
    MatSnackBarModule,
    MatDialogModule,
    FormsModule
  ],
  templateUrl: './product-list.html',
  styleUrls: ['./product-list.scss']
})
export class ProductList {
  displayedColumns: string[] = ['id', 'name', 'price', 'stock', 'acoes'];
  dataSource = new MatTableDataSource<any>();
  originalData: any[] = []; // mantém os dados originais da API

  // filtros
  textFilter: string = ''; 
  minPrice: number | null = null;
  maxPrice: number | null = null;
  minStock: number | null = null;

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild(MatSort) sort!: MatSort;

  constructor(
    private productService: ProductService,
    private router: Router,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.carregarProdutos();
  }

  carregarProdutos() {
    this.productService.getAll().subscribe({
      next: (res) => {
        this.originalData = res.data;
        this.dataSource.data = [...this.originalData];
        this.dataSource.paginator = this.paginator;
        this.dataSource.sort = this.sort;
      },
      error: (err) => {
        console.error('Erro ao carregar produtos:', err);
        this.snackBar.open('Erro ao carregar produtos.', 'Fechar', {
          duration: 3000,
          panelClass: ['snackbar-error']
        });
      }
    });
  }

  applyTextFilter() {
    this.dataSource.filter = this.textFilter.trim().toLowerCase();
  }


  applyFilter(event: Event) {
    const filterValue = (event.target as HTMLInputElement).value;
    this.dataSource.filter = filterValue.trim().toLowerCase();
  }

  applyAdvancedFilters() {
    let filtered = [...this.originalData];

    if (this.minPrice !== null) {
      filtered = filtered.filter(p => Number(p.price) >= this.minPrice!);
    }
    if (this.maxPrice !== null) {
      filtered = filtered.filter(p => Number(p.price) <= this.maxPrice!);
    }
    if (this.minStock !== null) {
      filtered = filtered.filter(p => Number(p.stock) >= this.minStock!);
    }

    this.dataSource.data = filtered;
  }

  clearFilters() {
    this.minPrice = null;
    this.maxPrice = null;
    this.minStock = null;
    this.textFilter = '';
    this.dataSource.filter = '';
    this.dataSource.data = [...this.originalData];
  }

  novoProduto() {
    this.router.navigate(['/products/new']);
  }

  editar(produto: any) {
    this.router.navigate(['/products/edit', produto.id]);
  }

  visualizar(produto: any) {
    this.router.navigate(['/products/view', produto.id]);
  }

  deletar(produto: any) {
    const dialogRef = this.dialog.open(ConfirmDialog, {
      width: '350px',
      data: { message: `Tem certeza que deseja excluir o produto ${produto.name}?` }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.productService.delete(produto.id).subscribe({
          next: () => {
            this.snackBar.open('Produto deletado com sucesso!', 'Fechar', {
              duration: 3000,
              panelClass: ['snackbar-success']
            });
            this.carregarProdutos();
          },
          error: (err) => {
            console.error('Erro ao deletar produto:', err);
            this.snackBar.open('Erro ao deletar produto.', 'Fechar', {
              duration: 3000,
              panelClass: ['snackbar-error']
            });
          }
        });
      }
    });
  }

  logout() {
    localStorage.removeItem('token');
    this.router.navigate(['/login']);
  }
}
