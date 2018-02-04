create extension intarray;

create table users (
   id serial primary key,
   role_id integer not null,
   username varchar(255) unique,
   first_name varchar(255),
   last_name varchar(255),
   active bool,
   inserted_at timestamp with time zone,
   updated_at timestamp with time zone
)

create table tags (
   id serial primary key,
   name varchar(255),
   inserted_at timestamp with time zone,
   updated_at timestamp with time zone
);

create table categories (
   id serial primary key,
   name varchar(255),
   inserted_at timestamp with time zone,
   updated_at timestamp with time zone,
);

create table products (
    sku varchar(255) primary key,
    name varchar(255),
    description text,
    status_id integer,
    regular_price numeric,
    discount_price numeric,
    category_id integer,
    quantity integer,
    taxable bool,
    tag_ids integer[],
    inserted_at timestamp with time zone,
    updated_at timestamp with time zone
);

create table coupons (
   id serial primary key,
   code varchar(255),
   description text,
   active bool,
   value numeric,
   start_date timestamp with time zone,
   end_date timestamp with time zone,
   multiple bool default false,
   inserted_at timestamp with time zone,
   updated_at  timestamp with time zone
);

create table orders (
   id serial primary key,
   order_date date,
   total numeric,
   coupon_id integer,
   user_id integer,
   inserted_at timestamp with time zone,
   updated_at timestamp with time zone
)